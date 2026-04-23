function updateOldDashboard() {
    fetch('get_slots_status.php')
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                const occupied = data.occupied_global || [];
                const rows = ['A', 'B', 'C', 'D', 'E'];
                rows.forEach(r => {
                    for(let i=1; i<=10; i++) {
                        let s = r + i;
                        let el = document.getElementById('slot-'+s);
                        if(el) {
                            if(occupied.includes(s)) {
                                el.className = 'slot busy';
                            } else {
                                el.className = 'slot free';
                            }
                        }
                    }
                });
            }
        })
        .catch(err => console.error(err));
}

// Auto refresh map every 3 seconds
setInterval(updateOldDashboard, 3000);
