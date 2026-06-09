const pool = require("../config/db");

const encontrarPorUsername = async (username) => {
  const [rows] = await pool.query(
    "SELECT * FROM usuarios WHERE username = ? AND activo = TRUE",
    [username]
  );
  return rows[0];
};

const encontrarPorId = async (id) => {
  const [rows] = await pool.query(
    "SELECT id, username, email, nombre_completo, rol, fecha_registro FROM usuarios WHERE id = ? AND activo = TRUE",
    [id]
  );
  return rows[0];
};

const crearUsuario = async ({ username, email, password, nombre_completo }) => {
  const [result] = await pool.query(
    "INSERT INTO usuarios (username, email, password, nombre_completo, rol) VALUES (?, ?, ?, ?, 'alumno')",
    [username, email, password, nombre_completo || ""]
  );
  return result.insertId;
};

const actualizarUltimoAcceso = async (id) => {
  await pool.query(
    "UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?",
    [id]
  );
};

module.exports = {
  encontrarPorUsername,
  encontrarPorId,
  crearUsuario,
  actualizarUltimoAcceso
};
