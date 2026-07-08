(function (root) {
  'use strict';

  const MODE_LABELS = {
    walk: 'Gå',
    minecart: 'Åk minecart',
    'boat-ice': 'Åk båt på is'
  };

  function buildGraph(network) {
    const destinations = new Map(network.destinations.map((destination) => [destination.id, destination]));
    const adjacency = new Map(network.destinations.map((destination) => [destination.id, []]));

    network.roads.forEach((road) => {
      const [first, second] = road.nodes;
      if (!destinations.has(first) || !destinations.has(second)) {
        throw new Error(`Vägen ${first}–${second} hänvisar till en okänd station.`);
      }

      const speed = network['travel-modes'][road['best-travel-mode']];
      if (!speed || road.distance < 0) {
        throw new Error(`Vägen ${first}–${second} har ogiltiga resedata.`);
      }

      adjacency.get(first).push({ destination: second, road });
      adjacency.get(second).push({ destination: first, road });
    });

    return { destinations, adjacency };
  }

  function findFastestRoute(network, source, destination) {
    const graph = buildGraph(network);
    if (!graph.destinations.has(source) || !graph.destinations.has(destination)) {
      return null;
    }

    const stationPenalty = network['station-penalty-seconds'];
    const distances = new Map([...graph.destinations.keys()].map((id) => [id, Infinity]));
    const previous = new Map();
    const unvisited = new Set(graph.destinations.keys());
    distances.set(source, stationPenalty);

    while (unvisited.size > 0) {
      let current = null;
      let currentDistance = Infinity;

      unvisited.forEach((id) => {
        if (distances.get(id) < currentDistance) {
          current = id;
          currentDistance = distances.get(id);
        }
      });

      if (current === null || currentDistance === Infinity) break;
      unvisited.delete(current);
      if (current === destination) break;

      graph.adjacency.get(current).forEach(({ destination: neighbour, road }) => {
        if (!unvisited.has(neighbour)) return;
        const speed = network['travel-modes'][road['best-travel-mode']];
        const travelSeconds = road.distance / speed;
        const candidate = currentDistance + travelSeconds + stationPenalty;

        if (candidate < distances.get(neighbour)) {
          distances.set(neighbour, candidate);
          previous.set(neighbour, { node: current, road, travelSeconds });
        }
      });
    }

    if (distances.get(destination) === Infinity) return null;

    const steps = [];
    let cursor = destination;
    while (cursor !== source) {
      const connection = previous.get(cursor);
      if (!connection) return null;
      steps.unshift({
        destination: graph.destinations.get(cursor),
        road: connection.road,
        travelSeconds: connection.travelSeconds
      });
      cursor = connection.node;
    }
    steps.unshift({ destination: graph.destinations.get(source), road: null, travelSeconds: 0 });

    const travelSeconds = steps.reduce((total, step) => total + step.travelSeconds, 0);
    return {
      steps,
      totalSeconds: distances.get(destination),
      travelSeconds,
      stationSeconds: steps.length * stationPenalty,
      stationPenaltySeconds: stationPenalty
    };
  }

  function findNearestStation(network, x, z, inNether) {
    if (!Number.isFinite(x) || !Number.isFinite(z)) return null;

    const coordinateScale = inNether ? 1 / 8 : 1;
    let nearest = null;

    network.destinations.forEach((destination) => {
      const stationX = destination.coordinates[0] * coordinateScale;
      const stationZ = destination.coordinates[1] * coordinateScale;
      const distance = Math.hypot(x - stationX, z - stationZ);
      if (!nearest || distance < nearest.distance) {
        nearest = { destination, distance };
      }
    });

    return nearest;
  }

  function coordinatesForDimension(destination, inNether) {
    const coordinateScale = inNether ? 1 / 8 : 1;
    return {
      x: destination.coordinates[0] * coordinateScale,
      z: destination.coordinates[1] * coordinateScale
    };
  }

  function formatDuration(seconds) {
    const rounded = Math.round(seconds);
    const minutes = Math.floor(rounded / 60);
    const remainder = rounded % 60;
    if (minutes === 0) return `${remainder} sek`;
    return `${minutes} min ${remainder} sek`;
  }

  function stationTransition(route, index) {
    if (index === route.steps.length - 1) {
      return { label: 'Passera portal', type: 'portal-end' };
    }

    const entersNetherHere = index === 0 && (!route.approach || !route.approach.inNether);
    if (entersNetherHere) {
      return { label: 'Passera portal', type: 'portal-start' };
    }

    return { label: 'Byte', type: 'transfer' };
  }

  function addOption(select, destination) {
    const option = document.createElement('option');
    option.value = destination.id;
    option.textContent = destination.name;
    select.appendChild(option);
  }

  function renderRoute(container, route) {
    container.replaceChildren();

    const summary = document.createElement('div');
    summary.className = 'nether-route-summary';
    const total = document.createElement('strong');
    total.textContent = formatDuration(route.totalSeconds);
    const detail = document.createElement('span');
    detail.textContent = `${formatDuration(route.travelSeconds)} restid + ${formatDuration(route.stationSeconds)} vid ${route.steps.length} stationer`;
    summary.append(total, detail);

    const list = document.createElement('ol');
    list.className = 'nether-route-steps';

    if (route.approach) {
      const item = document.createElement('li');
      item.className = 'nether-route-approach';
      const title = document.createElement('span');
      title.className = 'nether-route-step-title';
      const dimension = route.approach.inNether ? 'Nether' : 'Overworld';
      title.textContent = `Start: X ${route.approach.x.toLocaleString('sv-SE')}, Z ${route.approach.z.toLocaleString('sv-SE')} (${dimension})`;
      const leg = document.createElement('span');
      leg.className = 'nether-route-leg';
      const unit = route.approach.inNether ? 'Nether-block' : 'Overworld-block';
      leg.textContent = `Gå till ${route.approach.destination.name} · ${route.approach.distance.toLocaleString('sv-SE', { maximumFractionDigits: 1 })} ${unit} · ${formatDuration(route.approach.travelSeconds)}`;
      const stationCoordinates = document.createElement('span');
      stationCoordinates.className = 'nether-route-station-coordinates';
      stationCoordinates.textContent = `Stationens koordinater: X ${route.approach.stationCoordinates.x.toLocaleString('sv-SE', { maximumFractionDigits: 1 })}, Z ${route.approach.stationCoordinates.z.toLocaleString('sv-SE', { maximumFractionDigits: 1 })}`;
      item.append(title, leg, stationCoordinates);
      list.appendChild(item);
    }

    route.steps.forEach((step, index) => {
      const item = document.createElement('li');
      const transition = stationTransition(route, index);
      if (transition.type === 'portal-start') item.classList.add('nether-route-start');
      const title = document.createElement('span');
      title.className = 'nether-route-step-title';
      title.textContent = index === 0 && !route.approach
        ? `Start: ${step.destination.name}`
        : index === route.steps.length - 1
          ? `Framme: ${step.destination.name}`
          : step.destination.name;
      item.appendChild(title);

      const transfer = document.createElement('span');
      transfer.className = `nether-route-transfer ${transition.type}`;
      transfer.textContent = `${transition.label}: ${formatDuration(route.stationPenaltySeconds)}`;

      if (step.road) {
        const leg = document.createElement('span');
        leg.className = 'nether-route-leg';
        const mode = MODE_LABELS[step.road['best-travel-mode']] || step.road['best-travel-mode'];
        leg.textContent = `${mode} · ${step.road.distance.toLocaleString('sv-SE')} Nether-block · ${formatDuration(step.travelSeconds)}`;
        item.appendChild(leg);
      }
      item.appendChild(transfer);
      list.appendChild(item);
    });

    container.append(summary, list);
    container.hidden = false;
  }

  async function initialiseNavigator() {
    const form = document.getElementById('nether-route-form');
    if (!form) return;

    const from = document.getElementById('nether-route-from');
    const to = document.getElementById('nether-route-to');
    const swap = document.getElementById('nether-route-swap');
    const originModes = [...form.querySelectorAll('[name="origin-mode"]')];
    const originModeFieldset = form.querySelector('.nether-origin-mode');
    const stationOrigin = document.getElementById('nether-station-origin');
    const coordinateOrigin = document.getElementById('nether-coordinate-origin');
    const routeFields = document.getElementById('nether-route-fields');
    const coordinateX = document.getElementById('nether-route-x');
    const coordinateZ = document.getElementById('nether-route-z');
    const inNether = document.getElementById('nether-route-in-nether');
    const nearestStatus = document.getElementById('nether-nearest-station');
    const submit = form.querySelector('[type="submit"]');
    const status = document.getElementById('nether-route-status');
    const result = document.getElementById('nether-route-result');

    try {
      const response = await fetch('data/nether-network.json');
      if (!response.ok) throw new Error(`HTTP ${response.status}`);
      const network = await response.json();
      buildGraph(network);

      from.replaceChildren();
      to.replaceChildren();
      const placeholderFrom = new Option('Välj start', '');
      const placeholderTo = new Option('Välj destination', '');
      from.appendChild(placeholderFrom);
      to.appendChild(placeholderTo);
      [...network.destinations]
        .sort((a, b) => a.name.localeCompare(b.name, 'sv'))
        .forEach((destination) => {
          addOption(from, destination);
          addOption(to, destination);
        });

      from.disabled = false;
      to.disabled = false;
      swap.disabled = false;
      submit.disabled = false;
      originModeFieldset.disabled = false;
      status.textContent = `${network.destinations.length} stationer är redo.`;

      const usesCoordinates = () => form.elements['origin-mode'].value === 'coordinates';

      const updateNearestStation = () => {
        if (!usesCoordinates()) return null;
        if (coordinateX.value === '' || coordinateZ.value === '') {
          nearestStatus.textContent = 'Ange X och Z för att hitta närmaste station.';
          return null;
        }

        const nearest = findNearestStation(network, Number(coordinateX.value), Number(coordinateZ.value), inNether.checked);
        if (!nearest) return null;
        const unit = inNether.checked ? 'Nether-block' : 'Overworld-block';
        nearestStatus.textContent = `Närmsta station: ${nearest.destination.name} · ${nearest.distance.toLocaleString('sv-SE', { maximumFractionDigits: 1 })} ${unit} bort`;
        return nearest;
      };

      const updateOriginMode = () => {
        const coordinateMode = usesCoordinates();
        stationOrigin.hidden = coordinateMode;
        coordinateOrigin.hidden = !coordinateMode;
        from.disabled = coordinateMode;
        from.required = !coordinateMode;
        coordinateX.disabled = !coordinateMode;
        coordinateZ.disabled = !coordinateMode;
        inNether.disabled = !coordinateMode;
        coordinateX.required = coordinateMode;
        coordinateZ.required = coordinateMode;
        swap.hidden = coordinateMode;
        swap.disabled = coordinateMode;
        routeFields.classList.toggle('coordinates-mode', coordinateMode);
        if (coordinateMode) updateNearestStation();
      };

      originModes.forEach((radio) => radio.addEventListener('change', updateOriginMode));
      [coordinateX, coordinateZ, inNether].forEach((control) => control.addEventListener('input', updateNearestStation));

      swap.addEventListener('click', () => {
        const oldFrom = from.value;
        from.value = to.value;
        to.value = oldFrom;
      });

      form.addEventListener('submit', (event) => {
        event.preventDefault();
        result.hidden = true;
        if (!to.value || (!usesCoordinates() && !from.value)) {
          status.textContent = 'Välj både start och destination.';
          return;
        }
        if (!usesCoordinates() && from.value === to.value) {
          status.textContent = 'Du är redan framme – välj två olika stationer.';
          return;
        }

        let source = from.value;
        let approach = null;
        if (usesCoordinates()) {
          const nearest = updateNearestStation();
          if (!nearest) {
            status.textContent = 'Ange giltiga X- och Z-koordinater.';
            return;
          }
          const walkingSpeed = network['travel-modes'].walk;
          source = nearest.destination.id;
          approach = {
            x: Number(coordinateX.value),
            z: Number(coordinateZ.value),
            inNether: inNether.checked,
            destination: nearest.destination,
            stationCoordinates: coordinatesForDimension(nearest.destination, inNether.checked),
            distance: nearest.distance,
            travelSeconds: nearest.distance / walkingSpeed
          };
        }

        const route = findFastestRoute(network, source, to.value);
        if (!route) {
          status.textContent = 'Det finns ännu ingen registrerad väg mellan stationerna.';
          return;
        }

        if (approach) {
          route.approach = approach;
          route.totalSeconds += approach.travelSeconds;
          route.travelSeconds += approach.travelSeconds;
          status.textContent = `Gå först till ${approach.destination.name}, följ sedan ${route.steps.length - 1} delsträckor i Nether.`;
        } else {
          status.textContent = `Snabbaste vägen har ${route.steps.length - 1} delsträckor.`;
        }
        renderRoute(result, route);
      });
    } catch (error) {
      console.error('Kunde inte ladda Nether-navigatorn:', error);
      status.textContent = 'Navigatorn kunde inte laddas. Försök igen senare.';
    }
  }

  const api = { buildGraph, coordinatesForDimension, findFastestRoute, findNearestStation, formatDuration, stationTransition };
  if (typeof module !== 'undefined' && module.exports) module.exports = api;
  if (typeof document !== 'undefined') {
    document.addEventListener('DOMContentLoaded', initialiseNavigator);
  }
  root.NetherNavigator = api;
}(typeof window !== 'undefined' ? window : globalThis));
