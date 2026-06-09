const express = require("express");
const router = express.Router();
const ctrl = require("../controllers/diagramas.controller");
const { verificarToken } = require("../middleware/auth");

// Ruta PÚBLICA — sin login
router.get("/publicos", ctrl.listarPublicos);

// Rutas PRIVADAS — requieren token
router.get("/",           verificarToken, ctrl.listar);
router.get("/:id",        verificarToken, ctrl.obtener);
router.post("/",          verificarToken, ctrl.crear);
router.put("/:id",        verificarToken, ctrl.actualizar);
router.delete("/:id",     verificarToken, ctrl.eliminar);
router.post("/:id/duplicar", verificarToken, ctrl.duplicar);

module.exports = router;
