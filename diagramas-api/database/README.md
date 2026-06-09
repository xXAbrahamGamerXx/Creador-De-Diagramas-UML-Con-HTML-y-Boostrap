# API REST — Diagramas UML (v1.1.0)

API actualizada para usar la tabla paralela `diagramas_api`.
**No afecta el sistema MVC existente.**

---

## Paso 1 — Crear las tablas en MySQL

1. Abre **phpMyAdmin**
2. Selecciona la base de datos **diagramas_db**
3. Ve a la pestaña **SQL**
4. Copia y pega el contenido de `database/diagramas_api_tabla.sql`
5. Haz clic en **Ejecutar**

Listo. Se crean las tablas `diagramas_api` y `diagramas_api_historial`.

---

## Paso 2 — Instalar dependencias

```bash
cd diagramas-api
npm install
```

---

## Paso 3 — Crear el archivo .env

```bash
cp .env.example .env
```

Edita `.env` con tus datos reales:

```env
PORT=3000
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=           ← tu contraseña de MySQL (puede quedar vacío si no tienes)
DB_NAME=diagramas_db
DB_PORT=3306
JWT_SECRET=escribe_cualquier_texto_largo_aqui_12345
JWT_EXPIRES_IN=7d
```

---

## Paso 4 — Levantar la API

```bash
npm run dev
```

Deberías ver:
```
Servidor corriendo en http://localhost:3000
```

---

## Paso 5 — Probar en Postman

### Login (obtener token)
- **POST** `http://localhost:3000/api/auth/login`
- Body JSON:
```json
{
  "username": "alumno1",
  "password": "password"
}
```
- Copia el `token` de la respuesta.

### Ver mis diagramas
- **GET** `http://localhost:3000/api/diagramas`
- Header: `Authorization: Bearer TOKEN`

### Crear diagrama
- **POST** `http://localhost:3000/api/diagramas`
- Header: `Authorization: Bearer TOKEN`
- Body JSON:
```json
{
  "titulo": "Mi diagrama",
  "tipo_diagrama": "usecase",
  "contenido_json": "{\"elementos\": []}"
}
```

---

## Endpoints disponibles

| Método   | Ruta                          | Login | Descripción              |
|----------|-------------------------------|-------|--------------------------|
| GET      | `/`                           | No    | Verificar que funciona   |
| POST     | `/api/auth/register`          | No    | Registrar usuario        |
| POST     | `/api/auth/login`             | No    | Login — obtener token    |
| GET      | `/api/auth/perfil`            | Sí    | Ver mi perfil            |
| GET      | `/api/diagramas/publicos`     | No    | Diagramas compartidos    |
| GET      | `/api/diagramas`              | Sí    | Mis diagramas            |
| GET      | `/api/diagramas/:id`          | Sí    | Un diagrama por ID       |
| POST     | `/api/diagramas`              | Sí    | Crear diagrama           |
| PUT      | `/api/diagramas/:id`          | Sí    | Actualizar diagrama      |
| DELETE   | `/api/diagramas/:id`          | Sí    | Eliminar diagrama        |
| POST     | `/api/diagramas/:id/duplicar` | Sí    | Duplicar diagrama        |

---

## Estructura del proyecto

```
diagramas-api/
├── database/
│   └── diagramas_api_tabla.sql  ← Ejecutar esto en phpMyAdmin PRIMERO
├── src/
│   ├── config/
│   │   └── db.js                ← Conexión MySQL
│   ├── middleware/
│   │   └── auth.js              ← Verifica el token JWT
│   ├── models/
│   │   ├── diagramas.model.js   ← Usa tabla diagramas_api
│   │   └── usuarios.model.js    ← Usa tabla usuarios (la misma del MVC)
│   ├── controllers/
│   │   ├── auth.controller.js
│   │   └── diagramas.controller.js
│   ├── routes/
│   │   ├── auth.routes.js
│   │   └── diagramas.routes.js
│   ├── app.js                   ← Express + CORS + rate limit
│   └── index.js                 ← Levanta el servidor
├── .env.example
├── .gitignore
└── package.json
```
