'use strict';

const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const { buildGraph, coordinatesForDimension, findFastestRoute, findNearestStation, stationTransition } = require('../script/nether-navigator.js');

const network = JSON.parse(fs.readFileSync(path.join(__dirname, '../data/nether-network.json'), 'utf8'));

buildGraph(network);

const destinations = new Map(network.destinations.map((destination) => [destination.id, destination]));
network.roads.forEach((road) => {
  const [a, b] = road.nodes.map((id) => destinations.get(id).coordinates);
  const calculated = Math.round((Math.abs(a[0] - b[0]) + Math.abs(a[1] - b[1])) / 8);
  assert.equal(road.distance, calculated, `${road.nodes.join('–')} has the wrong distance`);
  assert.ok(Number.isInteger(road.distance), `${road.nodes.join('–')} must use a whole-number distance`);
});

const outbound = findFastestRoute(network, 'fabulania', 'the-rike');
const inbound = findFastestRoute(network, 'the-rike', 'fabulania');
assert.deepEqual(outbound.steps.map((step) => step.destination.id), ['fabulania', 'the-rike']);
assert.ok(Math.abs(outbound.totalSeconds - inbound.totalSeconds) < 1e-9, 'roads must work in both directions');
assert.equal(outbound.stationSeconds, 20, 'source and destination must each cost 10 seconds');
assert.equal(outbound.stationPenaltySeconds, 10, 'the route must expose its per-station transfer time');
assert.deepEqual(stationTransition(outbound, 0), { label: 'Passera portal', type: 'portal-start' });
assert.deepEqual(stationTransition(outbound, 1), { label: 'Passera portal', type: 'portal-end' });

const throughRoute = findFastestRoute(network, 'faburania', 'colosseum');
assert.deepEqual(stationTransition(throughRoute, 1), { label: 'Byte', type: 'transfer' });
throughRoute.approach = { inNether: true };
assert.deepEqual(stationTransition(throughRoute, 0), { label: 'Byte', type: 'transfer' });
throughRoute.approach.inNether = false;
assert.deepEqual(stationTransition(throughRoute, 0), { label: 'Passera portal', type: 'portal-start' });

const direct = findFastestRoute(network, 'fabulania', 'x-mines');
assert.equal(direct.stationSeconds, 20, 'a direct route charges both endpoint stations');
assert.ok(Math.abs(direct.totalSeconds - (20 + (88 / 30))) < 1e-9);

network.destinations.forEach((destination) => {
  if (destination.coordinates[0] === 0 && destination.coordinates[1] === 0) return;
  assert.ok(findFastestRoute(network, 'fabulania', destination.id), `${destination.name} is disconnected`);
});

const destinationIds = network.destinations.map((destination) => destination.id);
assert.equal(new Set(destinationIds).size, destinationIds.length, 'destination IDs must be unique');

const roadIds = network.roads.map((road) => [...road.nodes].sort().join('|'));
assert.equal(new Set(roadIds).size, roadIds.length, 'undirected roads must be unique');

const overworldNearest = findNearestStation(network, 3, -904, false);
assert.equal(overworldNearest.destination.id, 'fabulania');
assert.equal(overworldNearest.distance, 5);

const netherNearest = findNearestStation(network, 0, -112.5, true);
assert.equal(netherNearest.destination.id, 'fabulania');
assert.equal(netherNearest.distance, 0);
assert.deepEqual(coordinatesForDimension(netherNearest.destination, true), { x: 0, z: -112.5 });
assert.deepEqual(coordinatesForDimension(netherNearest.destination, false), { x: 0, z: -900 });

console.log(`Nether navigator tests passed (${network.destinations.length} destinations, ${network.roads.length} undirected roads).`);
