function renderizarTablaPedidos() {
    const tbody = document.getElementById('tabla-pedidos-body');
    tbody.innerHTML = '';

    const ventasOrdenadas = [...STATE.ventas].sort((a, b) => b.id - a.id);

    ventasOrdenadas.forEach(pedido => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>#${pedido.id}</td>
            <td>${pedido.fecha || 'Reciente'}</td>
            <td>${pedido.nombre_cliente || 'Cliente Desconocido'}</td> 
            <td class="fw-bold">$${parseFloat(pedido.total).toFixed(2)}</td>
            <td><span class="badge bg-info text-dark">${pedido.estado}</span></td>
            <td>
                <button class="btn btn-sm btn-primary" onclick="verDetalle(${pedido.id})">
                    <i class="bi bi-eye"></i> Ver Detalle
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function verDetalle(idPedido) {
    const pedido = STATE.ventas.find(p => p.id == idPedido);
    
    if(!pedido) return;

    document.getElementById('modal-pedido-id').textContent = idPedido;
    const tbody = document.getElementById('modal-detalle-body');
    tbody.innerHTML = '';

    if(pedido.items && pedido.items.length > 0) {
        pedido.items.forEach(item => {
            tbody.innerHTML += `
                <tr>
                    <td>${item.nombre}</td>
                    <td>$${parseFloat(item.precio).toFixed(2)}</td>
                    <td>${item.cantidad}</td>
                    <td class="fw-bold">$${parseFloat(item.subtotal).toFixed(2)}</td>
                </tr>
            `;
        });
    } else {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center">Sin detalles disponibles</td></tr>';
    }

    const modal = new bootstrap.Modal(document.getElementById('modalDetalle'));
    modal.show();
}