const bcrypt = require("bcryptjs");
const jwt = require("jsonwebtoken");
const usuarioModel = require("../models/usuarios.model");

// POST /api/auth/register
// Cualquier persona puede registrarse — queda como alumno por defecto
const register = async (req, res) => {
  try {
    const { username, email, password, nombre_completo } = req.body;

    if (!username || !email || !password) {
      return res.status(400).json({ error: "username, email y password son obligatorios" });
    }

    if (password.length < 6) {
      return res.status(400).json({ error: "La contraseña debe tener al menos 6 caracteres" });
    }

    // Encriptar contraseña
    const hash = await bcrypt.hash(password, 10);

    const id = await usuarioModel.crearUsuario({ username, email, password: hash, nombre_completo });

    res.status(201).json({
      mensaje: "Usuario registrado correctamente",
      id
    });
  } catch (error) {
    if (error.code === "ER_DUP_ENTRY") {
      return res.status(400).json({ error: "El username o email ya está en uso" });
    }
    console.error(error);
    res.status(500).json({ error: "Error al registrar usuario" });
  }
};

// POST /api/auth/login
// Devuelve un JWT que el cliente debe guardar y mandar en cada petición privada
const login = async (req, res) => {
  try {
    const { username, password } = req.body;

    if (!username || !password) {
      return res.status(400).json({ error: "username y password son obligatorios" });
    }

    const usuario = await usuarioModel.encontrarPorUsername(username);

    if (!usuario) {
      return res.status(401).json({ error: "Credenciales incorrectas" });
    }

    const passwordOk = await bcrypt.compare(password, usuario.password);

    if (!passwordOk) {
      return res.status(401).json({ error: "Credenciales incorrectas" });
    }

    await usuarioModel.actualizarUltimoAcceso(usuario.id);

    const token = jwt.sign(
      { id: usuario.id, username: usuario.username, rol: usuario.rol },
      process.env.JWT_SECRET,
      { expiresIn: process.env.JWT_EXPIRES_IN || "7d" }
    );

    res.json({
      mensaje: "Login exitoso",
      token,
      usuario: {
        id: usuario.id,
        username: usuario.username,
        nombre_completo: usuario.nombre_completo,
        rol: usuario.rol
      }
    });
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: "Error al iniciar sesión" });
  }
};

// GET /api/auth/perfil  (requiere token)
const perfil = async (req, res) => {
  try {
    const usuario = await usuarioModel.encontrarPorId(req.usuario.id);
    if (!usuario) return res.status(404).json({ error: "Usuario no encontrado" });
    res.json(usuario);
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: "Error al obtener perfil" });
  }
};

module.exports = { register, login, perfil };
