let charts = {}; 

async function actualizarDashboard() {
    try {
        const [promRes, devRes, corrRes] = await Promise.all([
            fetch(`${API_URL}/estadisticas/promedio-ventas`),
            fetch(`${API_URL}/estadisticas/desvio-estandar`),
            fetch(`${API_URL}/estadisticas/correlacion-precio`)
        ]);

        const promData = await promRes.json();
        const devData = await devRes.json();
        const corrData = await corrRes.json();

        document.getElementById('stat-promedio').innerText = `$${parseFloat(promData.promedio_ventas_diarias || 0).toFixed(2)}`;
        document.getElementById('stat-desvio').innerText = `$${parseFloat(devData.desvio_estandar || 0).toFixed(2)}`;
        document.getElementById('stat-corr').innerText = parseFloat(corrData.correlacion_precio_cantidad || 0).toFixed(4);

    } catch (e) {
        console.error("Error estadísticas", e);
    }

    renderizarGraficos();
}

function renderizarGraficos() {
    const ventas = STATE.ventas;
    if(ventas.length === 0) return;

    const prodMap = {};
    ventas.forEach(v => {
        if(v.items) {
            v.items.forEach(item => {
                if(!prodMap[item.nombre]) prodMap[item.nombre] = 0;
                prodMap[item.nombre] += parseFloat(item.subtotal);
            });
        }
    });
    crearChart('chartProductos', 'bar', Object.keys(prodMap), Object.values(prodMap), 'Ventas ($)');


    const fechaMap = {};
    ventas.forEach(v => {
        const fecha = v.fecha ? v.fecha.split(' ')[0] : 'Hoy';
        if(!fechaMap[fecha]) fechaMap[fecha] = 0;
        fechaMap[fecha] += parseFloat(v.total);
    });
    const fechasOrd = Object.keys(fechaMap).sort();
    const totalesOrd = fechasOrd.map(f => fechaMap[f]);
    crearChart('chartFechas', 'line', fechasOrd, totalesOrd, 'Evolución Diaria');


    const scatterData = [];
    ventas.forEach(v => {
        if(v.items) {
            v.items.forEach(item => {
                scatterData.push({
                    x: parseFloat(item.precio), 
                    y: parseInt(item.cantidad)  
                });
            });
        }
    });
    crearScatterChart('chartDispersion', scatterData);


    
    const pagoMap = {};
    ventas.forEach(v => {
        
        const metodo = v.estado || 'Desconocido';
        if(!pagoMap[metodo]) pagoMap[metodo] = 0;
        pagoMap[metodo] += 1; 
    });
    crearPieChart('chartMetodosPago', Object.keys(pagoMap), Object.values(pagoMap));
}


function crearChart(canvasId, tipo, labels, data, titulo) {
    const ctx = document.getElementById(canvasId);
    if(charts[canvasId]) charts[canvasId].destroy();

    charts[canvasId] = new Chart(ctx, {
        type: tipo,
        data: {
            labels: labels,
            datasets: [{
                label: titulo,
                data: data,
                backgroundColor: tipo === 'bar' ? '#0d6efd' : 'rgba(25, 135, 84, 0.2)',
                borderColor: tipo === 'line' ? '#198754' : 'none',
                borderWidth: 1,
                fill: true,
                tension: 0.3
            }]
        },
        options: { responsive: true }
    });
}


function crearScatterChart(canvasId, data) {
    const ctx = document.getElementById(canvasId);
    if(charts[canvasId]) charts[canvasId].destroy();

    charts[canvasId] = new Chart(ctx, {
        type: 'scatter',
        data: {
            datasets: [{
                label: 'Precio vs Cantidad',
                data: data,
                backgroundColor: 'rgba(255, 193, 7, 1)'
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: { title: { display: true, text: 'Precio Unitario ($)' } },
                y: { title: { display: true, text: 'Cantidad Unidades' }, beginAtZero: true }
            }
        }
    });
}


function crearPieChart(canvasId, labels, data) {
    const ctx = document.getElementById(canvasId);
    if(charts[canvasId]) charts[canvasId].destroy();

    charts[canvasId] = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: ['#0dcaf0', '#ffc107', '#dc3545', '#6610f2']
            }]
        },
        options: { responsive: true }
    });
}