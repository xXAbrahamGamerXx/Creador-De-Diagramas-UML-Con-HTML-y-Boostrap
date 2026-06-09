// src/models/diagramas.model.js
// Actualizado para usar la tabla paralela: diagramas_api
// Esta tabla es exclusiva para la API y NO afecta el sistema MVC local.

const pool = require("../config/db");

// ── Tabla que usa la API ──────────────────────────────────────────────
const TABLA = "diagramas_api";

// ────────────────────────────────────────────────────────────────────
// Listar diagramas del usuario con paginación y filtro
// ────────────────────────────────────────────────────────────────────
const listar = async (usuario_id, { filtro = "", pagina = 1, por_pagina = 12 } = {}) => {
  const offset   = (pagina - 1) * por_pagina;
  const busqueda = `%${filtro}%`;

  const [rows] = await pool.query(
    `SELECT id, titulo, descripcion, tipo_diagrama, etiquetas,
            fecha_creacion, fecha_modificacion, version, compartido
     FROM ${TABLA}
     WHERE usuario_id = ? AND (titulo LIKE ? OR etiquetas LIKE ?)
     ORDER BY fecha_modificacion DESC
     LIMIT ? OFFSET ?`,
    [usuario_id, busqueda, busqueda, por_pagina, offset]
  );

  const [[{ total }]] = await pool.query(
    `SELECT COUNT(*) as total FROM ${TABLA}
     WHERE usuario_id = ? AND (titulo LIKE ? OR etiquetas LIKE ?)`,
    [usuario_id, busqueda, busqueda]
  );

  return { diagramas: rows, total, pagina, por_pagina };
};

// ────────────────────────────────────────────────────────────────────
// Obtener un diagrama completo (incluye contenido_json)
// ────────────────────────────────────────────────────────────────────
const obtener = async (id, usuario_id) => {
  const [rows] = await pool.query(
    `SELECT * FROM ${TABLA} WHERE id = ? AND usuario_id = ?`,
    [id, usuario_id]
  );
  return rows[0];
};

// ────────────────────────────────────────────────────────────────────
// Crear diagrama nuevo
// ────────────────────────────────────────────────────────────────────
const crear = async ({
  usuario_id,
  titulo,
  descripcion,
  tipo_diagrama,
  contenido_json,
  etiquetas,
  diagrama_original_id = null
}) => {
  const [result] = await pool.query(
    `INSERT INTO ${TABLA}
       (usuario_id, titulo, descripcion, tipo_diagrama, contenido_json, etiquetas, diagrama_original_id)
     VALUES (?, ?, ?, ?, ?, ?, ?)`,
    [
      usuario_id,
      titulo,
      descripcion       || "",
      tipo_diagrama     || "usecase",
      contenido_json    || "{}",
      etiquetas         || "",
      diagrama_original_id
    ]
  );
  return result.insertId;
};

// ────────────────────────────────────────────────────────────────────
// Actualizar diagrama (incrementa versión automáticamente)
// ────────────────────────────────────────────────────────────────────
const actualizar = async (id, usuario_id, {
  titulo,
  descripcion,
  tipo_diagrama,
  contenido_json,
  etiquetas
}) => {
  await pool.query(
    `UPDATE ${TABLA}
     SET titulo = ?, descripcion = ?, tipo_diagrama = ?,
         contenido_json = ?, etiquetas = ?, version = version + 1
     WHERE id = ? AND usuario_id = ?`,
    [
      titulo,
      descripcion    || "",
      tipo_diagrama,
      contenido_json || "{}",
      etiquetas      || "",
      id,
      usuario_id
    ]
  );
};

// ────────────────────────────────────────────────────────────────────
// Eliminar diagrama
// ────────────────────────────────────────────────────────────────────
const eliminar = async (id, usuario_id) => {
  await pool.query(
    `DELETE FROM ${TABLA} WHERE id = ? AND usuario_id = ?`,
    [id, usuario_id]
  );
};

// ────────────────────────────────────────────────────────────────────
// Duplicar diagrama
// ────────────────────────────────────────────────────────────────────
const duplicar = async (id, usuario_id) => {
  const original = await obtener(id, usuario_id);
  if (!original) return null;

  const [result] = await pool.query(
    `INSERT INTO ${TABLA}
       (usuario_id, titulo, descripcion, tipo_diagrama, contenido_json, etiquetas)
     VALUES (?, ?, ?, ?, ?, ?)`,
    [
      usuario_id,
      `${original.titulo} (copia)`,
      original.descripcion,
      original.tipo_diagrama,
      original.contenido_json,
      original.etiquetas
    ]
  );
  return result.insertId;
};

// ────────────────────────────────────────────────────────────────────
// Listar públicos (compartido = TRUE) — sin login
// ────────────────────────────────────────────────────────────────────
const listarPublicos = async ({ filtro = "", pagina = 1, por_pagina = 12 } = {}) => {
  const offset   = (pagina - 1) * por_pagina;
  const busqueda = `%${filtro}%`;

  const [rows] = await pool.query(
    `SELECT d.id, d.titulo, d.descripcion, d.tipo_diagrama, d.etiquetas,
            d.fecha_modificacion, d.version,
            u.username, u.nombre_completo
     FROM ${TABLA} d
     JOIN usuarios u ON u.id = d.usuario_id
     WHERE d.compartido = TRUE AND (d.titulo LIKE ? OR d.etiquetas LIKE ?)
     ORDER BY d.fecha_modificacion DESC
     LIMIT ? OFFSET ?`,
    [busqueda, busqueda, por_pagina, offset]
  );

  const [[{ total }]] = await pool.query(
    `SELECT COUNT(*) as total FROM ${TABLA}
     WHERE compartido = TRUE AND (titulo LIKE ? OR etiquetas LIKE ?)`,
    [busqueda, busqueda]
  );

  return { diagramas: rows, total, pagina, por_pagina };
};

module.exports = {
  listar,
  obtener,
  crear,
  actualizar,
  eliminar,
  duplicar,
  listarPublicos
};
