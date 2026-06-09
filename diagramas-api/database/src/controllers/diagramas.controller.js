const diagramaModel = require("../models/diagramas.model");

// ── RUTAS PÚBLICAS ───────────────────────────────────────────

// GET /api/diagramas/publicos
// Sin login — lista diagramas marcados como compartidos
const listarPublicos = async (req, res) => {
  try {
    const { filtro = "", pagina = 1, por_pagina = 12 } = req.query;
    const resultado = await diagramaModel.listarPublicos({
      filtro,
      pagina: Number(pagina),
      por_pagina: Number(por_pagina)
    });
    res.json(resultado);
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: "Error al obtener diagramas públicos" });
  }
};

// ── RUTAS PRIVADAS (requieren token) ────────────────────────

// GET /api/diagramas
const listar = async (req, res) => {
  try {
    const { filtro = "", pagina = 1, por_pagina = 12 } = req.query;
    const resultado = await diagramaModel.listar(req.usuario.id, {
      filtro,
      pagina: Number(pagina),
      por_pagina: Number(por_pagina)
    });
    res.json(resultado);
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: "Error al listar diagramas" });
  }
};

// GET /api/diagramas/:id
const obtener = async (req, res) => {
  try {
    const diagrama = await diagramaModel.obtener(req.params.id, req.usuario.id);
    if (!diagrama) return res.status(404).json({ error: "Diagrama no encontrado" });
    res.json(diagrama);
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: "Error al obtener diagrama" });
  }
};

// POST /api/diagramas
const crear = async (req, res) => {
  try {
    const { titulo, descripcion, tipo_diagrama, contenido_json, etiquetas } = req.body;

    if (!titulo || titulo.trim() === "") {
      return res.status(400).json({ error: "El título es obligatorio" });
    }

    const id = await diagramaModel.crear({
      usuario_id: req.usuario.id,
      titulo: titulo.trim(),
      descripcion,
      tipo_diagrama,
      contenido_json,
      etiquetas
    });

    res.status(201).json({ mensaje: "Diagrama creado correctamente", id });
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: "Error al crear diagrama" });
  }
};

// PUT /api/diagramas/:id
const actualizar = async (req, res) => {
  try {
    const diagrama = await diagramaModel.obtener(req.params.id, req.usuario.id);
    if (!diagrama) return res.status(404).json({ error: "Diagrama no encontrado" });

    const { titulo, descripcion, tipo_diagrama, contenido_json, etiquetas } = req.body;

    if (!titulo || titulo.trim() === "") {
      return res.status(400).json({ error: "El título es obligatorio" });
    }

    await diagramaModel.actualizar(req.params.id, req.usuario.id, {
      titulo: titulo.trim(),
      descripcion,
      tipo_diagrama,
      contenido_json,
      etiquetas
    });

    res.json({ mensaje: "Diagrama actualizado correctamente" });
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: "Error al actualizar diagrama" });
  }
};

// DELETE /api/diagramas/:id
const eliminar = async (req, res) => {
  try {
    const diagrama = await diagramaModel.obtener(req.params.id, req.usuario.id);
    if (!diagrama) return res.status(404).json({ error: "Diagrama no encontrado" });

    await diagramaModel.eliminar(req.params.id, req.usuario.id);
    res.json({ mensaje: "Diagrama eliminado correctamente" });
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: "Error al eliminar diagrama" });
  }
};

// POST /api/diagramas/:id/duplicar
const duplicar = async (req, res) => {
  try {
    const nuevoId = await diagramaModel.duplicar(req.params.id, req.usuario.id);
    if (!nuevoId) return res.status(404).json({ error: "Diagrama no encontrado" });
    res.status(201).json({ mensaje: "Diagrama duplicado correctamente", id: nuevoId });
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: "Error al duplicar diagrama" });
  }
};

module.exports = { listarPublicos, listar, obtener, crear, actualizar, eliminar, duplicar };
