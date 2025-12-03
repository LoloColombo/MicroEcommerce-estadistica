const API_URL = 'http://localhost/Ecommerce-Estadisticas/api';

let STATE = {
    productos: [],
    clientes: [],
    ventas: [] 
};

document.addEventListener('DOMContentLoaded', () => {
    cargarDatosApp();
});

async function cargarDatosApp() {
    try {
        const [prodRes, cliRes] = await Promise.all([
            fetch(`${API_URL}/productos`),
            fetch(`${API_URL}/clientes`)
        ]);

        STATE.productos = await prodRes.json();
        STATE.clientes = await cliRes.json();

        const pedidosRes = await fetch(`${API_URL}/pedidos`);
        const pedidosSimple = await pedidosRes.json();

        
        const promesasDetalle = pedidosSimple.map(async (pedido) => {
            try {
                const detRes = await fetch(`${API_URL}/pedidos/${pedido.id}`);
                const detalles = await detRes.json();
                return { ...pedido, items: detalles }; 
            } catch (e) {
                console.error(`Error cargando detalle pedido ${pedido.id}`, e);
                return { ...pedido, items: [] };
            }
        });

        STATE.ventas = await Promise.all(promesasDetalle);

        console.log("Datos cargados:", STATE);

        document.dispatchEvent(new Event('datos-cargados'));

        if (typeof renderizarTablaPedidos === 'function') {
            renderizarTablaPedidos();
        }

    } catch (error) {
        console.error("Error crítico cargando datos:", error);
        mostrarAlerta("Error de conexión con la API. Revisa que XAMPP esté activo.", "danger");
    }
}

function mostrarAlerta(msg, tipo) {
    const div = document.getElementById('alerta-global');
    div.innerHTML = `<div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
                        ${msg}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>`;
    setTimeout(() => div.innerHTML = '', 4000);
}

