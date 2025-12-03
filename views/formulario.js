document.addEventListener('datos-cargados', () => {
    llenarSelects();
});

const formVenta = document.getElementById('formVenta');
const formCliente = document.getElementById('formCliente');
const selProducto = document.getElementById('venta-producto');
const inputPrecio = document.getElementById('precioUnitario');
const inputCantidad = document.getElementById('cantidad');
const displayTotal = document.getElementById('totalVenta');

function llenarSelects() {
    const selCliente = document.getElementById('venta-cliente');
    
    selCliente.innerHTML = '<option value="">Seleccione Cliente...</option>';
    selProducto.innerHTML = '<option value="">Seleccione Producto...</option>';

    STATE.clientes.forEach(c => {
        selCliente.innerHTML += `<option value="${c.id}">${c.nombre}</option>`;
    });

    STATE.productos.forEach(p => {
        selProducto.innerHTML += `<option value="${p.id}" data-precio="${p.precio}">${p.nombre}</option>`;
    });
}

selProducto.addEventListener('change', (e) => {
    const opcion = e.target.selectedOptions[0];
    const precio = opcion.getAttribute('data-precio');
    
    if(precio) {
        inputPrecio.value = precio; 
    } else {
        inputPrecio.value = '';
    }
    calcularTotal();
});

inputCantidad.addEventListener('input', calcularTotal);

function calcularTotal() {
    const p = parseFloat(inputPrecio.value) || 0;
    const c = parseInt(inputCantidad.value) || 0;
    displayTotal.textContent = `$${(p * c).toFixed(2)}`;
}

formVenta.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const datosVenta = {
        id_cliente: document.getElementById('venta-cliente').value,
        estado: document.getElementById('metodoPago').value, 
        items: [
            {
                id_producto: selProducto.value,
                cantidad: inputCantidad.value
            }
        ]
    };

    try {
        const res = await fetch(`${API_URL}/pedidos`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(datosVenta)
        });

        if(res.ok) {
            mostrarAlerta("¡Venta registrada con éxito!", "success");
            formVenta.reset();
            document.getElementById('fecha').valueAsDate = new Date(); 
            inputPrecio.value = '';
            displayTotal.textContent = '$0.00';
            cargarDatosApp(); 
        } else {
            throw new Error("Error en API");
        }
    } catch (error) {
        mostrarAlerta("Error al registrar venta", "danger");
    }
});

formCliente.addEventListener('submit', async (e) => {
    e.preventDefault();

    const nuevoCliente = {
        nombre: document.getElementById('cli-nombre').value,
        email: document.getElementById('cli-email').value,
        direccion: document.getElementById('cli-direccion').value
    };

    try {
        const res = await fetch(`${API_URL}/clientes`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(nuevoCliente)
        });

        if(res.ok) {
            mostrarAlerta("Cliente creado correctamente", "success");
            formCliente.reset();
            cargarDatosApp(); 
        } else {
            mostrarAlerta("Faltan datos o error en servidor", "warning");
        }
    } catch (error) {
        mostrarAlerta("Error de conexión", "danger");
    }
});


document.getElementById('fecha').valueAsDate = new Date();