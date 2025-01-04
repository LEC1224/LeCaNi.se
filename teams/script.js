
document.addEventListener('DOMContentLoaded', function() {
    const randomizeButton = document.getElementById('randomize');
    const balanceButton = document.getElementById('balance');

    randomizeButton.addEventListener('click', function() {
        createTeams('random');
    });

    balanceButton.addEventListener('click', function() {
        createTeams('balanced');
    });
});

function createTeams(mode) {
    let players = [];
    for (let i = 1; i <= 10; i++) {
        const playerName = document.getElementById('player' + i).value;
        const playerElo = parseInt(document.getElementById('elo' + i).value) || 0;
        if (playerName) {
            players.push({ name: playerName, elo: playerElo });
        }
    }

    let teamBlue = [];
    let teamBeige = [];
    let totalEloBlue = 0;
    let totalEloBeige = 0;

    if (mode === 'random') {
        players = players.sort(() => 0.5 - Math.random());
        players.forEach((player, index) => {
            if (index % 2 === 0) {
                teamBlue.push(player);
                totalEloBlue += player.elo;
            } else {
                teamBeige.push(player);
                totalEloBeige += player.elo;
            }
        });
    } else if (mode === 'balanced') {
        // Balancing teams based on ELO
        players = players.sort((a, b) => b.elo - a.elo);
        players.forEach(player => {
            if (totalEloBlue <= totalEloBeige) {
                teamBlue.push(player);
                totalEloBlue += player.elo;
            } else {
                teamBeige.push(player);
                totalEloBeige += player.elo;
            }
        });
    }

    displayTeams(teamBlue, teamBeige, totalEloBlue, totalEloBeige);
}

function displayTeams(teamBlue, teamBeige, totalEloBlue, totalEloBeige) {
    const teamBlueDiv = document.getElementById('teamBlue').querySelector('.player-list');
    const teamBeigeDiv = document.getElementById('teamBeige').querySelector('.player-list');

    teamBlueDiv.innerHTML = teamBlue.map(player => '<div>' + player.name + ' (' + player.elo + ')</div>').join('');
    teamBeigeDiv.innerHTML = teamBeige.map(player => '<div>' + player.name + ' (' + player.elo + ')</div>').join('');

    document.getElementById('eloBlue').textContent = totalEloBlue;
    document.getElementById('eloBeige').textContent = totalEloBeige;
    document.getElementById('eloDifference').textContent = Math.abs(totalEloBlue - totalEloBeige);
}
