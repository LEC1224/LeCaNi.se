'use strict';

const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const { buildGraph, findFastestRoute } = require('../script/nether-navigator.js');

const network = JSON.parse(fs.readFileSync(path.join(__dirname, '../data/nether-network.json'), 'utf8'));

buildGraph(network);

const destinations = new Map(network.destinations.map((destination) => [destination.id, destination]));
network.roads.forEach((road) => {
  const [a, b] = road.nodes.map((id) => destinations.get(id).coordinates);
  const calculated = Math.hypot(a[0] - b[0], a[1] - b[1]) / 8;
  assert.ok(Math.abs(calculated - road.distance) <= 0.11, `${road.nodes.join('–')} has the wrong distance`);
});

const outbound = findFastestRoute(network, 'fabulania', 'the-rike');
const inbound = findFastestRoute(network, 'the-rike', 'fabulania');
assert.deepEqual(outbound.steps.map((step) => step.destination.id), ['fabulania', 'the-rike']);
assert.ok(Math.abs(outbound.totalSeconds - inbound.totalSeconds) < 1e-9, 'roads must work in both directions');
assert.equal(outbound.stationSeconds, 20, 'source and destination must each cost 10 seconds');

const direct = findFastestRoute(network, 'fabulania', 'x-mines');
assert.equal(direct.stationSeconds, 20, 'a direct route charges both endpoint stations');
assert.ok(Math.abs(direct.totalSeconds - (20 + (62.5 / 30))) < 1e-9);

network.destinations.forEach((destination) => {
  if (destination.coordinates[0] === 0 && destination.coordinates[1] === 0) return;
  assert.ok(findFastestRoute(network, 'fabulania', destination.id), `${destination.name} is disconnected`);
});

const destinationIds = network.destinations.map((destination) => destination.id);
assert.equal(new Set(destinationIds).size, destinationIds.length, 'destination IDs must be unique');

const roadIds = network.roads.map((road) => [...road.nodes].sort().join('|'));
assert.equal(new Set(roadIds).size, roadIds.length, 'undirected roads must be unique');

console.log(`Nether navigator tests passed (${network.destinations.length} destinations, ${network.roads.length} undirected roads).`);
