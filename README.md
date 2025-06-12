# eRPair

¡Bienvenido a **eRPair**!  
Una plataforma innovadora para la gestión eficiente de reparaciones electrónicas.
---

## 📋 Requisitos

- PHP >= 8.2
- Composer
- MySQL o MariaDB
- Conexión a internet
- Navegador

---
---

## 🚀 Características

- Gestión ordenes de trabajo
- Seguimiento del estado de la reparacion de los dispotivos
- Base de datos de marcas, modelos, items, etc
- Gestion de facturacion.
- Generacion de total facturado
- Otros...

---

## 🛠️ Instalación

1. Clona el repositorio:
    ```bash
    git clone https://github.com/tu-usuario/eRPair.git
    ```
2. Instala las dependencias:
    ```bash
    composer i 
    ```
3. Inicia la aplicación:
    ```bash
    cp .env.example .env 
    ```
4. Genera la clave de la aplicación:
    ```bash
    php artisan key:generate
    ```
5. Configura la contraseña de tu usuario de MySQL en el archivo `.env`:
    ```
    DB_PASSWORD=tu_contraseña_mysql
    ```
6. Ejecuta las migraciones y los seeders:
    ```bash
    php artisan migrate:fresh --seed
    ```
7. Inicia el servidor de desarrollo:
    ```bash
    php artisan serve
    ```
---
## MODULOS

## 💵 Cajas (cashDesk)

El módulo de cajas permite llevar un control detallado de los movimientos de efectivo dentro del sistema.  

- Cerrar cajas diarias.
- Registrar ingresos totales de efectivo y tarjeta.
- Cálculo automático de ingresos totales.
---
## 👥 Clientes
El módulo de clientes permite gestionar la información de tus clientes de manera eficiente.

- Registrar nuevos clientes.
- Editar la información de clientes existentes.
- Consultar la lista de dispositivos asociados a cada cliente.

---
## 👤 Usuarios/Mi Usuario

El módulo de usuarios permite gestionar la información de los usuarios del sistema.

- Registrar nuevos usuarios.
- Editar la información de usuarios existentes.
- Consultar o editar la lista de roles o tiendas asociados a cada usuario.

**En caso de no ser administrador, el usuario podrá editar únicamente su propia información.**
---

## 📝 Hoja de Pedidos

El módulo de **Hoja de Pedidos** te ayuda a gestionar fácilmente todas las órdenes de trabajo. 

- Edita pedidos existentes antes de 30 minutos.
- Consulta el estado.
- Facturas asociadas.
- Visualiza los estados, el cierre y los ítems incluidos en cada pedido.
- 👷‍♂️  **Técnico**, puede: 
   - Asignarte pedidos para gestionarlos.
    - Cambiar el estado del pedido a "Pendiente de Pieza" si es necesario.
    - Realizar un cierre (reparado)
- 🧑‍💼 **Dependiente, encargado o admin**  puede:
    - Crear nuevos pedidos a través del cliente.
    - Realizar cobros (anticipados o finales).
    - Agregar Devoluciones.
    - Agregar Garantías.
- 🧑‍💻 **Común:** 
    - Consultar historial de facturas.
    - Consultar historial de estatus.
    - Consultar o modificar items asociados.
    - Cancelar pedido.
    - Acción de "info" para ver detalles del pedido.
---
## 🏷️ Marcas

El módulo de marcas te permite gestionar las marcas de los dispositivos de manera eficiente.

- Registrar nuevas marcas.
- Editar la información de marcas existentes.
- Consultar la lista de modelos asociados a cada marca.
- Asociar y editar la lista de modelos vinculados a cada marca.
- Acceder a un modelo, editar los ítems asociados y modificar el stock en las distintas tiendas.Marcas
---
## 📱 Dispositivos
El módulo de dispositivos te permite gestionar los dispositivos de manera eficiente.

- Consultar la lista de dispositivos.
- Acceder a los pedidos asociados a cada dispositivo.

---
## 🛠️ Ítems
El módulo de ítems te permite gestionar los ítems de manera eficiente.
- Consultar, agregar y editar la lista de ítems.

---
## 📂 Miscelanea

El sistema incluye varias tablas tipo que permiten únicamente agregar o listar registros, facilitando la gestión de información estructurada:
- 🗂️ **Categorías:** Organiza los dispositivos, ítems o servicios en diferentes categorías para una mejor clasificación.
- ⏱️ **Tiempos de reparación:** Define y consulta los tiempos estándar de reparación para distintos tipos de trabajos.
- 💸 **Impuestos:** Gestiona los diferentes tipos de impuestos aplicables a las facturas.
- 🏷️ **Tipo de ítems:** Clasifica los ítems según su naturaleza (por ejemplo, repuesto, accesorio, servicio, etc.).

---
## 🏢 Empresas
El módulo de empresas permite gestionar la información de las empresas asociadas al sistema.
- Registrar nuevas empresas.
- Editar la información de empresas existentes.

---
## 🧾 Facturas
El módulo de facturas te permite gestionar las facturas de manera eficiente.
- Consultar la lista de facturas.
- Permite solo editar método o empresa asociada a la factura.
- Exportar facturas a PDF.

---
## 🧾 Datos Fiscales
El módulo de datos fiscales permite gestionar la información fiscal de tu empresa.  
- Registrar los datos fiscales de tu empresa.
- Editar la información fiscal existente.
- Consultar la información fiscal asociada a las facturas.
---
## 🏬 Tiendas
El módulo de tiendas permite gestionar la información de las tiendas asociadas al sistema.
- Registrar nuevas tiendas.
- Editar la información de tiendas existentes.
---

## 📖 Manual de Usuario

A continuación, se describen los pasos básicos para utilizar la plataforma **eRPair**:

### 1. Iniciar sesión
Accede con tus credenciales proporcionadas por el administrador.
Una vez iniciado, elige la tienda y rol con el que deseas acceder.

### 2. Navegación principal
Utiliza el menú lateral para acceder a los diferentes módulos: Pedidos, Clientes, Facturas, Cajas, etc. El buscador te permite encontrar rápidamente pedidos o clientes.

## 3. Modulos:
 - Segun el tipo de rol que tenga el usuario podrá acceder a diferentes módulos y funcionalidades. A continuación, se detallan los módulos más relevantes:
 - **DEPENDIENTE:** 
    - Pagina de inicio / barra superior: En la barra superior te permitirá ver usuario y rol con el que has iniciado sesión, más solo en la pagina de inicio podrás ver la tienda en la que estas logueado.
    - Accede a los módulos: **Cajas**, **Clientes**, **Mi Usuario**, **Hojas de Pedidos**, **Marcas**, **Marcas>Modelos** **Ítems** y **Facturas**.

- **ENCARGADO:**
    Tiene los mismo privilegios que el dependiente, pero además tiene acceso a **Tiendas** y **Dispositivos**

- **ADMIN:**
    Tiene acceso a todos los módulos y funcionalidades del sistema, **excluyendo** los modulos de **Estados**, **Metodos de pago** y **Roles** para prevenir errores en la aplicación. Tambien tiene acceso a la exportacion de las facturas en PDF del mes junto con widgets de facturacion (efectivo hoy, tarjeta hoy, total mensual) 

- **TECNICO:**
    - Pagina de inicio / barra superior: En la barra superior te permitirá ver usuario y rol con el que has iniciado sesión, más solo en la pagina de inicio podrás ver la tienda en la que estas logueado.
    - Accede a los módulos: **Cajas**, **Clientes**, **Mi Usuario**, **Hojas de Pedidos**, **Marcas**, **Ítems** y **Facturas**.
    - Podrá asignarse pedidos para gestionarlos, cambiar el estado del pedido a "Pendiente de Pieza" si es necesario y realizar un cierre (reparado) del pedido una vez se lo asigne.

### 4. Crear una orden de trabajo:
- Busca un cliente por nombre o documento de identidad en el buscador o en el modulo de Clientes. En caso de no existir, puedes crear uno nuevo.
- Una vez creado, dentro del cliente, haz clic en **"Crear Dispositivo"**.
- Dentro de dispositivo, podras encontrar los pedidos asociados o en su defecto **"Crear Pedido"**. Rellena los campos necesarios, una vez creado el pedido.
**DEPENDIENTE, ENCARGADO o ADMIN**: Puedes realizar las siguientes acciones:
  - Ver el PDF del pedido.  
  - Registrar cobros anticipados o finales.
  - Cancelar el pedido.
  - En las secciones inferiores podras: 
    - Añadir items.
    - Ver las facturas asociadas.
    - Consultar el historial de estados del pedido.
    - En caso de estar cerrado, podrás ver el cierre del pedido.

- **TECNICO**: Si eres un técnico, puedes:
    - Ver PDF del pedido.
    - Asignarte pedidos para cerrarlos.
    - Cambiar el estado del pedido a "Pendiente de Pieza" si es necesario.
    - Realizar un cierre (reparado) del pedido una ves te lo asignes.


Para más detalles, consulta la documentación interna o contacta con el administrador del sistema.

Desarrollado por [Ismael Rodriguez Cuenca](https://github.com/ismaelrodcuenca)  

