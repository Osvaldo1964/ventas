document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    
    if(loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('btnLogin');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin ms-2"></i> Validando...';
            btn.disabled = true;

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            try {
                const response = await fetch('/ventas/public/api/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, password, tenant_id: 1 }) 
                });
                
                const data = await response.json();
                
                if(!response.ok || data.error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de acceso',
                        text: data.message || 'Credenciales incorrectas',
                        confirmButtonColor: '#2b3990'
                    });
                } else {
                    // Save JWT and user metadata
                    localStorage.setItem('pos_token', data.token);
                    localStorage.setItem('pos_usuario', JSON.stringify(data.usuario));

                    if(data.caja_status === 'require_open_caja') {
                        Swal.fire({
                            icon: 'info',
                            title: '¡Hola ' + data.usuario.nombre + '!',
                            text: 'Debes aperturar la caja antes de realizar cualquier operación.',
                            confirmButtonColor: '#2b3990',
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        }).then(() => {
                            window.location.href = '/ventas/public/cajas/operacion';
                        });
                    } else if (data.caja_status === 'require_close_caja') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Cierre Pendiente',
                            text: 'Tienes una sesión de caja anterior que no ha sido cerrada. Debes cerrarla para continuar.',
                            confirmButtonColor: '#2b3990',
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        }).then(() => {
                            window.location.href = '/ventas/public/cajas/operacion';
                        });
                    } else {
                        // Acceso normal (Admin o Cajero con caja abierta hoy)
                        Swal.fire({
                            icon: 'success',
                            title: '¡Bienvenido!',
                            text: 'Redirigiendo...',
                            timer: 1500,
                            showConfirmButton: false,
                            allowOutsideClick: false
                        }).then(() => {
                            window.location.href = '/ventas/public/dashboard';
                        });
                    }
                }
            } catch (err) {
                console.error(err);
                Swal.fire('Error de Servidor', 'Revisa que tu BD y API estén corriendo.', 'error');
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        });
    }
});
