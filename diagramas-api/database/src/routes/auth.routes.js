const express = require("express");
const router = express.Router();
const authController = require("../controllers/auth.controller");
const { verificarToken } = require("../middleware/auth");

// Rutas públicas — sin token
router.post("/register", authController.register);
router.post("/login",    authController.login);

// Ruta privada — con token
router.get("/perfil", verificarToken, authController.perfil);

module.exports = router;
