// src/app.js — Versión actualizada con mejoras de seguridad

const express   = require("express");
const cors      = require("cors");
const morgan    = require("morgan");
const rateLimit = require("express-rate-limit");

const authRoutes      = require("./routes/auth.routes");
const diagramasRoutes = require("./routes/diagramas.routes");

const app = express();

// ── CORS restringido ──────────────────────────────────────────────────
app.use(cors({
  origin: [
    "http://localhost:8080",
    "http://localhost:80",
    "http://localhost",
    "http://127.0.0.1:8080",
    "http://127.0.0.1"
  ],
  methods: ["GET", "POST", "PUT", "DELETE"],
  allowedHeaders: ["Content-Type", "Authorization"]
}));

// ── Rate limiting — máximo 100 requests por IP cada 15 minutos ────────
const limiter = rateLimit({
  windowMs: 15 * 60 * 1000,
  max: 100,
  standardHeaders: true,
  legacyHeaders: false,
  message: { error: "Demasiadas peticiones. Intenta de nuevo en 15 minutos." }
});
app.use("/api/", limiter);

// ── Middlewares básicos ───────────────────────────────────────────────
app.use(morgan("dev"));
app.use(express.json());
app.use(express.urlencoded({ extended: false }));

// ── Ruta raíz ─────────────────────────────────────────────────────────
app.get("/", (req, res) => {
  res.json({
    mensaje: "API de Diagramas UML funcionando correctamente",
    version: "1.1.0",
    endpoints: {
      auth:      "/api/auth",
      diagramas: "/api/diagramas"
    }
  });
});

// ── Rutas ─────────────────────────────────────────────────────────────
app.use("/api/auth",      authRoutes);
app.use("/api/diagramas", diagramasRoutes);

// ── 404 global ────────────────────────────────────────────────────────
app.use((req, res) => {
  res.status(404).json({ error: "Ruta no encontrada" });
});

// ── Manejador global de errores ───────────────────────────────────────
app.use((err, req, res, next) => {
  console.error("Error no manejado:", err);
  res.status(500).json({ error: "Error interno del servidor" });
});

module.exports = app;
