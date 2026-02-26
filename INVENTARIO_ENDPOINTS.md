# Inventario de Endpoints - lupsoft2-api

Fuente: [`routes/web.php`](/Users/cesaraugustovargas/Git-projects/lupsoft2-api/routes/web.php)

## Notas
- Salvo los endpoints de autenticación/login raíz y descarga pública, el resto pasan por middleware `auth`.
- Los ejemplos de `body` y `response` se basan en controladores/tests del proyecto.

## Endpoints

| Método | Ruta | Auth | Body ejemplo | Response ejemplo |
|---|---|---|---|---|
| GET | `/upload/documentosAdjuntos/{filepath:.*}` | No | N/A | Archivo binario (`200`) o `{"error":"File not found"}` (`404`) |
| GET | `/` | No | N/A | `"Lumen (x.y.z)"` |
| POST | `/auth/register` | No | `{"name":"Juan","email":"juan@mail.com","password":"secret","password_confirmation":"secret"}` | `{"user":{...},"message":"CREATED"}` |
| POST | `/auth/login` | No | `{"email":"juan@mail.com","password":"secret"}` | `{"id":1,"name":"Juan","email":"juan@mail.com","access_token":"...","token_type":"bearer","status":"success","menu_usuario":[...],"permisos":[...],"expires_in":3600}` |
| POST | `/auth/logout` | No | N/A | `{"message":"Sesión cerrada correctamente"}` |
| GET | `/profile` | Sí | N/A | `{"user":{...}}` |
| GET | `/users/{id}` | Sí | N/A | `{"user":{...}}` |
| GET | `/users` | Sí | N/A | `{"users":[...]}` |
| GET | `/cobradores/{id}` | Sí | N/A | `[{"value":2,"label":"Cobrador 1"}]` |
| POST | `/psclientes/{id_empresa}` | Sí | N/A | `[{"id":1,"nomcliente":"...","id_empresa":1,...}]` |
| GET | `/psclientes/{id}` | Sí | N/A | `{"id":1,"nomcliente":"..."}` o `{"message":"Cliente no encontrado"}` |
| POST | `/psclientes` | Sí | `{"nomcliente":"Ana","id_tipo_docid":1,"numdocumento":"123","ciudad":"Bogotá","celular":"300...","id_empresa":1,"id_cobrador":2,"id_user":1,"email":"ana@mail.com","fch_expdocumento":"2024-01-01","fch_nacimiento":"1990-01-01"}` | Cliente creado (`201`) |
| PUT | `/psclientes/{id}` | Sí | `{"nomcliente":"Ana Actualizada","celular":"301..."}` | Cliente actualizado (`200`) |
| DELETE | `/psclientes/{id}` | Sí | N/A | `{"message":"Deleted Successfully"}` |
| GET | `/listadoclientes/{id}` | Sí | N/A | `[{"value":1,"label":"Ana"}]` |
| GET | `/pstipodocidenti` | Sí | N/A | `[{"value":1,"label":"Cédula"}]` |
| POST | `/pstipodocidenti` | Sí | `{"codtipdocid":1,"nomtipodocumento":"Cédula"}` | Tipo doc creado (`201`) |
| PUT | `/pstipodocidenti/{id}` | Sí | `{"nomtipodocumento":"Pasaporte"}` | Tipo doc actualizado |
| DELETE | `/pstipodocidenti/{id}` | Sí | N/A | `{"message":"Deleted Successfully"}` |
| GET | `/listaformaspago/{id_empresa}` | Sí | N/A | `[{"value":1,"label":"Mensual"}]` |
| GET | `/psperiodopago` | Sí | N/A | `[{"id":1,"nomperiodopago":"Mensual","id_empresa":1}]` |
| GET | `/psperiodopago/{id}` | Sí | N/A | `{"id":1,"nomperiodopago":"Mensual","id_empresa":1}` |
| POST | `/psperiodopago` | Sí | `{"nomperiodopago":"Quincenal","id_empresa":1}` | Período creado (`201`) |
| PUT | `/psperiodopago/{id}` | Sí | `{"nomperiodopago":"Semanal"}` | Período actualizado |
| DELETE | `/psperiodopago/{id}` | Sí | N/A | `{"message":"Deleted Successfully"}` |
| GET | `/listaperiodopago` | Sí | N/A | `[{"value":1,"label":"Mensual"}]` |
| GET | `/pstdocadjuntos` | Sí | N/A | `[{"id":1,"nombre":"Contrato","id_empresa":1}]` |
| GET | `/pstdocadjuntos/{id}` | Sí | N/A | `{"id":1,"nombre":"Contrato","id_empresa":1}` |
| POST | `/pstdocadjuntos` | Sí | `{"nombre":"Contrato","id_empresa":1}` | Tipo adjunto creado |
| PUT | `/pstdocadjuntos/{id}` | Sí | `{"nombre":"Pagaré"}` | Tipo adjunto actualizado |
| DELETE | `/pstdocadjuntos/{id}` | Sí | N/A | `{"message":"Deleted Successfully"}` |
| GET | `/listatdocadjuntos/{id_empresa}` | Sí | N/A | `[{"value":1,"label":"Contrato"}]` |
| GET | `/pstdocplant` | Sí | N/A | `[{"id":1,"nombre":"Plantilla 1","plantilla_html":"...","id_empresa":1}]` |
| GET | `/pstdocplant/{id}` | Sí | N/A | `[{"value":1,"label":"Documento"}]` |
| POST | `/pstdocplant` | Sí | `{"nombre":"Contrato","plantilla_html":"<p>Hola {nomcliente}</p>","id_empresa":1}` | Plantilla creada (`201`) |
| PUT | `/pstdocplant/{id}` | Sí | `{"nombre":"Contrato v2"}` | Plantilla actualizada |
| DELETE | `/pstdocplant/{id}` | Sí | N/A | `{"message":"Deleted Successfully"}` |
| GET | `/pspagos` | Sí | N/A | `[{"id":1,"id_cliente":1,"id_prestamo":99,"valcuota":150000,...}]` |
| POST | `/pspagos` | Sí | `{"id":10,"id_cliente":1,"id_user":1,"id_empresa":1,"id_prestamo":99,"fecha_pago":"2025-03-31","fecha":"2025-03-31 10:00:00"}` | `{"success":"Pago registrado correctamente"}` |
| PUT | `/pspagos/{id}` | Sí | `{"valcuota":9999}` | Pago actualizado |
| DELETE | `/pspagos/{id}` | Sí | N/A | `{"message":"Deleted Successfully"}` |
| GET | `/psfechaspago/{id_prestamo}` | Sí | N/A | `[{"id":1,"id_cliente":1,"id_prestamo":99,"fecha_pago":"Lunes, 25 de Diciembre de 2023","valcuota":"10,000.00","valtotal":"12,000.00","id_fecha_pago":null,"fecha_realpago":"Pendiente de pago"}]` |
| POST | `/psfechaspago` | Sí | `{"id_prestamo":99,"valor_cuota":10000,"valor_pagar":12000,"fecha_pago":"2025-03-31","ind_renovar":0,"ind_estado":1,"id_cliente":1,"id_empresa":1}` | Fecha de pago creada |
| PUT | `/psfechaspago/{id}` | Sí | `{"ind_renovar":1}` | Fecha de pago actualizada |
| DELETE | `/psfechaspago/{id}` | Sí | N/A | `{"message":"Deleted Successfully"}` |
| GET | `/psdocadjuntos` | Sí | N/A | `[{"id":1,"rutaadjunto":"upload/documentosAdjuntos/x.jpg","id_tdocadjunto":3,"id_cliente":1,...}]` |
| GET | `/psdocadjuntos/{id}` | Sí | N/A | `[{"id_cliente":1,...}]` |
| POST | `/psdocadjuntos` | Sí | `{"rutaadjunto":"upload/documentosAdjuntos/a.pdf","id_tdocadjunto":1,"id_usu_cargarch":1,"id_cliente":1,"nombrearchivo":"a.pdf","id_empresa":1}` | Adjunto creado (`201`) |
| PUT | `/psdocadjuntos/{id}` | Sí | `{"nombrearchivo":"nuevo.pdf"}` | Adjunto actualizado |
| DELETE | `/psdocadjuntos/{id}` | Sí | N/A | `{"message":"Deleted Successfully"}` |
| GET | `/pstiposistemaprest` | Sí | N/A | `[{"id":1,"codtipsistemap":1,"nomtipsistemap":"Francés","formula":"..."}]` |
| GET | `/pstiposistemaprest/{id}` | Sí | N/A | `[{"value":1,"label":"Mensual"}]` |
| POST | `/pstiposistemaprest` | Sí | `{"codtipsistemap":1,"nomtipsistemap":"Francés","formula":"return [...];"}` | Sistema préstamo creado |
| PUT | `/pstiposistemaprest/{id}` | Sí | `{"nomtipsistemap":"Alemán"}` | Sistema préstamo actualizado |
| DELETE | `/pstiposistemaprest/{id}` | Sí | N/A | `{"message":"Deleted Successfully"}` |
| GET | `/listatiposistemaprest/` | Sí | N/A | `[{"value":1,"label":"Francés"}]` |
| GET | `/psempresa/{id}` | Sí | N/A | `{"id":1,"nombre":"Empresa Demo","nitempresa":"123","firma":"https://.../upload/documentosAdjuntos/3-....png",...}` |
| PUT | `/psempresa/{id}` | Sí | `{"nombre":"Empresa X","nit":"900123","ddirec":"Calle 1","ciudad":"Bogotá","telefono":"300...","pagina":"https://...","email":"empresa@mail.com","vlr_capinicial":1000000,"firma":"data:image/png;base64,..."}` | Empresa actualizada |
| POST | `/consultaTipoDocPlantilla` | Sí | `{"id_empresa":1}` | `[{"id":1,"nombre":"Contrato","plantilla_html":"...","id_empresa":1}]` |
| POST | `/calcularCuotas` | Sí | `{"id_periodo_pago":1,"id_sistema_pago":"SIS01","numcuotas":12,"porcint":5,"valorpres":100000}` | `[{"cuota":1,"valor":10000,...}]` |
| POST | `/calcularCuotas2` | Sí | `{"id_periodo_pago":1,"id_sistema_pago":"SIS01","numcuotas":12,"porcint":5,"valorpres":100000}` | `{"cuota":10000,...}` |
| POST | `/listadoPrestamos` | Sí | `{"id_empresa":1}` | `[{"id_prestamo":1,"nomcliente":"...","valorpres":"..."}]` |
| POST | `/prestamosCliente` | Sí | `{"id_empresa":1,"id_cliente":1}` | `[{"Codigo Prestamo":99,"Numero Cuotas":12,"Valor Prestamo":"100,000.00","Valor Total Prestamo":"120,000.00","Abonos capital":"10,000.00","Total Abonado":"30,000.00","Saldo":"80,000.00"}]` |
| POST | `/renderTemplates` | Sí | `{"id_empresa":1,"id_prestamo":99}` | `[{"id":1,"nombre":"Contrato","plantilla_html":"<html renderizado>","id_empresa":1}]` |
| POST | `/guardarPrestamo` | Sí | `{"id_empresa":1,"id_cliente":1,"valorpres":100000,"numcuotas":12,"porcint":5,"id_periodo_pago":1,"id_sistema_pago":1,"fec_inicial":"2025-03-01","id_cobrador":2,"id_usureg":1,"fecha":"2025-03-01 10:00:00"}` | `99` (id préstamo) |
| GET | `/generarVariablesPlantillas/{id_empresa}` | Sí | N/A | `[{"title":"id","content":"{id}"},{"title":"nomcliente","content":"{nomcliente}"}]` |
| POST | `/guardarArchivoAdjunto` | Sí | `{"id_tdocadjunto":3,"id_empresa":1,"id_cliente":1,"id_usuario":1,"filename":"firma.png","image":"data:image/png;base64,..."}` | `{"status":"success","data":"upload/documentosAdjuntos/3-....png"}` |
| PUT | `/editarArchivoAdjunto` | Sí | `{"id_cliente":1,"id_usuario":1,"id_empresa":1,"filename":"archivo.png","id_tdocadjunto":[1,2],"image":["data:image/png;base64,...","https://..."]}` | Sin contenido explícito (`null`) |
| DELETE | `/eliminarPrestamo/{id_prestamo}` | Sí | N/A | Sin contenido explícito (soft delete) |
| GET | `/capitalprestado/{id_empresa}` | Sí | N/A | `"500,000.00"` o `"NA"` |
| POST | `/totalprestadohoy` | Sí | `{"id_empresa":1,"fecha":"2025-03-31"}` | `"12,345.68"` o `"NA"` |
| POST | `/totalintereshoy` | Sí | `{"id_empresa":1,"fecha":"2025-03-31"}` | `"3,500.00"` |
| POST | `/totalinteres` | Sí | `{"id_empresa":1}` | `"90,000.00"` |
| GET | `/totalprestado/{id_empresa}` | Sí | N/A | `"500,000.00"` o `"NA"` |
| POST | `/totales_dashboard` | Sí | `{"id_empresa":1,"fecha":"2025-03-31"}` | `{"total_capital_prestado":"...","total_interes":"...","total_interes_hoy":"...","total_prestado_hoy":"...","total_prestado":"...","ahora":"2026-02-26 12:34:56"}` |

