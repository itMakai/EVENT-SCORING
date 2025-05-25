function refreshScoreboard() {
    const backendUrl = 'https://scoreboard.infy.uk/backend';
    fetch(`${backendUrl}/api/scoreboard.php`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.text(); // Get raw text for debugging
        })
        .then(text => {
            console.log('Raw API Response:', text); // Log raw response
            let data;
            try {
                data = JSON.parse(text); // Attempt to parse
            } catch (parseError) {
                throw new Error('Failed to parse JSON response');
            }

            if (!Array.isArray(data)) {
                throw new Error('Unexpected API response format');
            }

            const table = document.getElementById('scoreboard');
            if (!table) {
                throw new Error('Scoreboard table element not found');
            }

            table.innerHTML = '<tr><th>Rank</th><th>User</th><th>Total Score</th></tr>';
            data.forEach((row, index) => {
                const tr = document.createElement('tr');
                tr.className = index < 3 ? `highlight-top-${index + 1}` : '';
                tr.innerHTML = `<td>${index + 1}</td><td>${row.display_name || 'N/A'}</td><td>${row.total_score || 0}</td>`;
                table.appendChild(tr);
            });
        })
        .catch(error => {
            console.error('Error refreshing scoreboard:', error);

            // display an error message to the user
            const table = document.getElementById('scoreboard');
            if (table) {
                table.innerHTML = '<tr><td colspan="3">Failed to load scoreboard. Please try again later.</td></tr>';
            }
        });
}
refreshScoreboard();
setInterval(refreshScoreboard, 10000);