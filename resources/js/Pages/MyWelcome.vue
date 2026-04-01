<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: { type: Boolean },
    canRegister: { type: Boolean },
    laravelVersion: { type: String, required: true },
    phpVersion: { type: String, required: true },
});
</script>

<template>
    <Head title="PolicyFlow — Creación de Políticas" />

    <div class="pf-root">

        <!-- Noise texture overlay -->
        <div class="pf-noise"></div>

        <!-- Grid pattern background -->
        <div class="pf-grid-bg"></div>

        <!-- Gradient orbs -->
        <div class="pf-orb pf-orb--1"></div>
        <div class="pf-orb pf-orb--2"></div>
        <div class="pf-orb pf-orb--3"></div>

        <!-- ───── NAVBAR ───── -->
        <nav class="pf-nav">
            <div class="pf-nav__inner">
                <div class="pf-logo">
                    <div class="pf-logo__icon">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2m-6 9 2 2 4-4"/></svg>
                    </div>
                    <span class="pf-logo__name">PolicyFlow</span>
                    <span class="pf-logo__badge">v2.0</span>
                </div>

                <div class="pf-nav__links">
                    <a href="#features" class="pf-nav__link">Características</a>
                    <a href="#diagram" class="pf-nav__link">Cómo funciona</a>
                    <a href="#" class="pf-nav__link">Documentación</a>
                </div>

                <div class="pf-nav__actions" v-if="canLogin">
                    <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="pf-btn pf-btn--primary">
                        Ir al Dashboard →
                    </Link>
                    <template v-else>
                        <Link :href="route('login')" class="pf-btn pf-btn--ghost">Ingresar</Link>
                        <Link v-if="canRegister" :href="route('register')" class="pf-btn pf-btn--primary">Registrarse</Link>
                    </template>
                </div>
            </div>
        </nav>

        <!-- ───── HERO ───── -->
        <section class="pf-hero">
            <div class="pf-hero__tag">
                <span class="pf-pulse"></span>
                Plataforma de Gestión Institucional
            </div>

            <h1 class="pf-hero__title">
                Políticas claras.<br>
                <span class="pf-gradient-text">Flujos visuales.</span>
            </h1>

            <p class="pf-hero__sub">
                Diseña, valida y publica políticas institucionales mediante <strong>diagramas de actividad con calles (swimlanes)</strong>. Cada rol, cada paso, perfectamente documentado.
            </p>

            <div class="pf-hero__cta">
                <Link v-if="canLogin && !$page.props.auth.user" :href="route('register')" class="pf-btn pf-btn--large pf-btn--primary">
                    Empezar gratis
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 12h14M12 5l7 7-7 7"/></svg>
                </Link>
                <a href="#diagram" class="pf-btn pf-btn--large pf-btn--outline">Ver demostración</a>
            </div>

            <div class="pf-hero__stats">
                <div class="pf-stat">
                    <span class="pf-stat__num">98%</span>
                    <span class="pf-stat__label">Claridad en procesos</span>
                </div>
                <div class="pf-stat__divider"></div>
                <div class="pf-stat">
                    <span class="pf-stat__num">3x</span>
                    <span class="pf-stat__label">Más rápido que Word</span>
                </div>
                <div class="pf-stat__divider"></div>
                <div class="pf-stat">
                    <span class="pf-stat__num">100%</span>
                    <span class="pf-stat__label">Trazable y auditable</span>
                </div>
            </div>
        </section>

        <!-- ───── SWIMLANE DIAGRAM ───── -->
        <section id="diagram" class="pf-section">
            <div class="pf-section__label">Diagrama de Actividad</div>
            <h2 class="pf-section__title">Proceso de Creación de Políticas</h2>
            <p class="pf-section__sub">Visualiza cada etapa del proceso organizada por responsable en su propia calle.</p>

            <div class="pf-diagram">
                <!-- Header de calles -->
                <div class="pf-diagram__header">
                    <div class="pf-diagram__lane-title pf-lane--blue">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0zM12 14a7 7 0 0 0-7 7h14a7 7 0 0 0-7-7z"/></svg>
                        Proponente
                    </div>
                    <div class="pf-diagram__lane-title pf-lane--violet">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2"/></svg>
                        Revisor
                    </div>
                    <div class="pf-diagram__lane-title pf-lane--emerald">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0 1 12 2.944a11.955 11.955 0 0 1-8.618 3.04A12.02 12.02 0 0 0 3 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Aprobador
                    </div>
                </div>

                <!-- Cuerpo del diagrama -->
                <div class="pf-diagram__body">
                    <!-- CALLE 1: Proponente -->
                    <div class="pf-lane pf-lane--blue-bg">
                        <!-- Inicio -->
                        <div class="pf-node pf-node--start">
                            <div class="pf-node__start-circle"></div>
                            <span class="pf-node__label">Inicio</span>
                        </div>
                        <div class="pf-arrow"></div>

                        <div class="pf-node pf-node--action">
                            <div class="pf-node__icon pf-node__icon--blue">
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2v-5m-1.414-9.414a2 2 0 1 1 2.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </div>
                            <span>Redactar borrador</span>
                        </div>
                        <div class="pf-arrow"></div>

                        <div class="pf-node pf-node--action">
                            <div class="pf-node__icon pf-node__icon--blue">
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-1m-4-8-4-4m0 0L8 8m4-4v12"/></svg>
                            </div>
                            <span>Enviar a revisión</span>
                        </div>
                        <div class="pf-arrow pf-arrow--cross-right"></div>

                        <div class="pf-node pf-node--action pf-node--dashed">
                            <div class="pf-node__icon pf-node__icon--orange">
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 0 0 4.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 0 1-15.357-2m15.357 2H15"/></svg>
                            </div>
                            <span>Ajustar según observaciones</span>
                        </div>
                    </div>

                    <!-- CALLE 2: Revisor -->
                    <div class="pf-lane pf-lane--violet-bg">
                        <div class="pf-lane__spacer"></div>

                        <div class="pf-node pf-node--action">
                            <div class="pf-node__icon pf-node__icon--violet">
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 1 1-14 0 7 7 0 0 1 14 0z"/></svg>
                            </div>
                            <span>Revisar contenido</span>
                        </div>
                        <div class="pf-arrow"></div>

                        <div class="pf-node pf-node--decision">
                            <div class="pf-decision-diamond">
                                <span>¿Aprobado?</span>
                            </div>
                        </div>

                        <div class="pf-decision-branches">
                            <div class="pf-branch">
                                <span class="pf-branch__label pf-branch__label--no">No</span>
                                <div class="pf-arrow pf-arrow--cross-left"></div>
                            </div>
                            <div class="pf-branch">
                                <span class="pf-branch__label pf-branch__label--yes">Sí</span>
                                <div class="pf-arrow pf-arrow--cross-right"></div>
                            </div>
                        </div>
                    </div>

                    <!-- CALLE 3: Aprobador -->
                    <div class="pf-lane pf-lane--emerald-bg">
                        <div class="pf-lane__spacer pf-lane__spacer--lg"></div>

                        <div class="pf-node pf-node--action">
                            <div class="pf-node__icon pf-node__icon--emerald">
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 0 0 1.946-.806 3.42 3.42 0 0 1 4.438 0 3.42 3.42 0 0 0 1.946.806 3.42 3.42 0 0 1 3.138 3.138 3.42 3.42 0 0 0 .806 1.946 3.42 3.42 0 0 1 0 4.438 3.42 3.42 0 0 0-.806 1.946 3.42 3.42 0 0 1-3.138 3.138 3.42 3.42 0 0 0-1.946.806 3.42 3.42 0 0 1-4.438 0 3.42 3.42 0 0 0-1.946-.806 3.42 3.42 0 0 1-3.138-3.138 3.42 3.42 0 0 0-.806-1.946 3.42 3.42 0 0 1 0-4.438 3.42 3.42 0 0 0 .806-1.946 3.42 3.42 0 0 1 3.138-3.138z"/></svg>
                            </div>
                            <span>Firma y aprobación</span>
                        </div>
                        <div class="pf-arrow"></div>

                        <div class="pf-node pf-node--action pf-node--highlight">
                            <div class="pf-node__icon pf-node__icon--emerald">
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-6l-2-2H5a2 2 0 0 0-2 2z"/></svg>
                            </div>
                            <span>Publicar política</span>
                        </div>
                        <div class="pf-arrow"></div>

                        <!-- Fin -->
                        <div class="pf-node pf-node--end">
                            <div class="pf-node__end-circle"></div>
                            <span class="pf-node__label">Fin</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ───── FEATURES ───── -->
        <section id="features" class="pf-section pf-section--features">
            <div class="pf-section__label">Por qué elegirnos</div>
            <h2 class="pf-section__title">Todo lo que necesitas para gestionar políticas</h2>

            <div class="pf-features">
                <div class="pf-feature">
                    <div class="pf-feature__icon pf-feature__icon--blue">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5zM4 13a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v6a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-6zM16 13a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v6a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-6z"/></svg>
                    </div>
                    <h3 class="pf-feature__title">Diagrama de Actividad Visual</h3>
                    <p class="pf-feature__desc">Construye flujos con editor drag-and-drop. Nodos, decisiones, forks y joins según el estándar UML 2.x.</p>
                </div>

                <div class="pf-feature">
                    <div class="pf-feature__icon pf-feature__icon--violet">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 0 0-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 0 1 5.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 0 1 9.288 0M15 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0zm6 3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM7 10a2 2 0 1 1-4 0 2 2 0 0 1 4 0z"/></svg>
                    </div>
                    <h3 class="pf-feature__title">Calles por Responsable</h3>
                    <p class="pf-feature__desc">Cada actor en su swimlane. Proponente, revisor, aprobador y publicador con límites claros de responsabilidad.</p>
                </div>

                <div class="pf-feature">
                    <div class="pf-feature__icon pf-feature__icon--emerald">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0 1 12 2.944a11.955 11.955 0 0 1-8.618 3.04A12.02 12.02 0 0 0 3 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h3 class="pf-feature__title">Validación Automática</h3>
                    <p class="pf-feature__desc">Motor de reglas que verifica coherencia, nomenclatura y cumplimiento normativo antes de publicar.</p>
                </div>

                <div class="pf-feature">
                    <div class="pf-feature__icon pf-feature__icon--amber">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                    </div>
                    <h3 class="pf-feature__title">Historial y Versiones</h3>
                    <p class="pf-feature__desc">Cada revisión queda registrada. Compara versiones, revierte cambios y mantén una trazabilidad completa.</p>
                </div>

                <div class="pf-feature">
                    <div class="pf-feature__icon pf-feature__icon--rose">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-1m-4-4-4 4m0 0-4-4m4 4V4"/></svg>
                    </div>
                    <h3 class="pf-feature__title">Exportar a PDF / Word</h3>
                    <p class="pf-feature__desc">Genera documentos de política bien formateados listos para firma, publicación o archivo institucional.</p>
                </div>

                <div class="pf-feature">
                    <div class="pf-feature__icon pf-feature__icon--cyan">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0 1 18 14.158V11a6.002 6.002 0 0 0-4-5.659V5a2 2 0 1 0-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 1 1-6 0v-1m6 0H9"/></svg>
                    </div>
                    <h3 class="pf-feature__title">Notificaciones en Tiempo Real</h3>
                    <p class="pf-feature__desc">Alertas automáticas por correo y sistema cuando una política requiere tu acción o cambia de estado.</p>
                </div>
            </div>
        </section>

        <!-- ───── FOOTER ───── -->
        <footer class="pf-footer">
            <div class="pf-footer__inner">
                <div class="pf-logo">
                    <div class="pf-logo__icon">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2m-6 9 2 2 4-4"/></svg>
                    </div>
                    <span class="pf-logo__name">PolicyFlow</span>
                </div>

                <p class="pf-footer__copy">
                    Laravel v{{ laravelVersion }} · PHP v{{ phpVersion }} · © 2026 PolicyFlow
                </p>
            </div>
        </footer>

    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');

/* ─── ROOT ─── */
.pf-root {
    font-family: 'Inter', 'Segoe UI', sans-serif;
    background-color: #09090b;
    color: #e4e4e7;
    min-height: 100vh;
    position: relative;
    overflow-x: hidden;
}

/* ─── NOISE ─── */
.pf-noise {
    pointer-events: none;
    position: fixed;
    inset: 0;
    z-index: 1;
    opacity: .028;
    background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)'/%3E%3C/svg%3E");
    background-repeat: repeat;
    background-size: 128px;
}

/* ─── GRID BG ─── */
.pf-grid-bg {
    pointer-events: none;
    position: fixed;
    inset: 0;
    z-index: 0;
    background-image:
        linear-gradient(rgba(255 255 255 / .03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255 255 255 / .03) 1px, transparent 1px);
    background-size: 48px 48px;
    mask-image: radial-gradient(ellipse 80% 60% at 50% 0%, black 40%, transparent 100%);
}

/* ─── ORBS ─── */
.pf-orb {
    pointer-events: none;
    position: fixed;
    border-radius: 50%;
    filter: blur(120px);
    z-index: 0;
}
.pf-orb--1 { width: 700px; height: 500px; top: -200px; left: -200px; background: radial-gradient(circle, rgba(99,102,241,.18) 0%, transparent 70%); }
.pf-orb--2 { width: 500px; height: 500px; top: 20%; right: -150px; background: radial-gradient(circle, rgba(139,92,246,.12) 0%, transparent 70%); }
.pf-orb--3 { width: 600px; height: 400px; bottom: 10%; left: 20%; background: radial-gradient(circle, rgba(16,185,129,.08) 0%, transparent 70%); }

/* ─── LAYOUT WRAPPERS ─── */
.pf-root > * { position: relative; z-index: 2; }

/* ─── NAV ─── */
.pf-nav {
    position: sticky;
    top: 0;
    z-index: 100;
    border-bottom: 1px solid rgba(255 255 255 / .06);
    background: rgba(9 9 11 / .7);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
}
.pf-nav__inner {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 24px;
    height: 60px;
    display: flex;
    align-items: center;
    gap: 40px;
}
.pf-nav__links { display: flex; gap: 4px; margin: 0 auto; }
.pf-nav__link {
    padding: 6px 14px;
    font-size: 14px;
    font-weight: 500;
    color: #a1a1aa;
    text-decoration: none;
    border-radius: 8px;
    transition: color .15s, background .15s;
}
.pf-nav__link:hover { color: #fff; background: rgba(255 255 255 / .06); }
.pf-nav__actions { display: flex; gap: 8px; align-items: center; }

/* ─── LOGO ─── */
.pf-logo { display: flex; align-items: center; gap: 10px; text-decoration: none; }
.pf-logo__icon {
    width: 34px; height: 34px;
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    color: white;
    box-shadow: 0 0 0 1px rgba(99,102,241,.3), 0 4px 12px rgba(99,102,241,.2);
    flex-shrink: 0;
}
.pf-logo__name { font-size: 16px; font-weight: 700; color: #fff; letter-spacing: -.02em; }
.pf-logo__badge {
    font-size: 10px; font-weight: 600;
    padding: 2px 7px;
    background: rgba(99,102,241,.15);
    color: #818cf8;
    border: 1px solid rgba(99,102,241,.25);
    border-radius: 20px;
}

/* ─── BUTTONS ─── */
.pf-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 18px;
    font-size: 14px; font-weight: 600;
    border-radius: 10px;
    text-decoration: none;
    transition: all .2s;
    cursor: pointer; border: none; outline: none;
    -webkit-font-smoothing: antialiased;
}
.pf-btn--ghost { color: #a1a1aa; background: transparent; }
.pf-btn--ghost:hover { color: #fff; background: rgba(255 255 255 / .07); }
.pf-btn--primary {
    color: #fff;
    background: linear-gradient(135deg, #6366f1 0%, #7c3aed 100%);
    box-shadow: 0 1px 0 rgba(255 255 255 / .1) inset, 0 4px 16px rgba(99,102,241,.3);
}
.pf-btn--primary:hover { transform: translateY(-1px); box-shadow: 0 1px 0 rgba(255 255 255 / .15) inset, 0 8px 24px rgba(99,102,241,.4); }
.pf-btn--outline {
    color: #e4e4e7;
    background: transparent;
    border: 1px solid rgba(255 255 255 / .12);
}
.pf-btn--outline:hover { border-color: rgba(255 255 255 / .25); background: rgba(255 255 255 / .04); transform: translateY(-1px); }
.pf-btn--large { padding: 12px 28px; font-size: 15px; border-radius: 12px; }

/* ─── HERO ─── */
.pf-hero {
    max-width: 900px;
    margin: 0 auto;
    padding: 52px 24px 52px;
    text-align: center;
}
.pf-hero__tag {
    display: inline-flex; align-items: center; gap: 8px;
    font-size: 12px; font-weight: 600;
    color: #818cf8;
    background: rgba(99,102,241,.08);
    border: 1px solid rgba(99,102,241,.2);
    padding: 6px 14px;
    border-radius: 20px;
    margin-bottom: 32px;
    letter-spacing: .04em; text-transform: uppercase;
}
.pf-pulse {
    width: 7px; height: 7px;
    background: #6366f1;
    border-radius: 50%;
    position: relative;
    flex-shrink: 0;
}
.pf-pulse::before {
    content: '';
    position: absolute;
    inset: -3px;
    border-radius: 50%;
    background: rgba(99,102,241,.4);
    animation: pulse-ring 2s ease-out infinite;
}
@keyframes pulse-ring { 0%,100%{transform:scale(1);opacity:.6} 50%{transform:scale(1.8);opacity:0} }

.pf-hero__title {
    font-size: clamp(42px, 7vw, 76px);
    font-weight: 900;
    line-height: 1.05;
    letter-spacing: -.04em;
    color: #fff;
    margin: 0 0 24px;
}
.pf-gradient-text {
    background: linear-gradient(135deg, #818cf8 0%, #a78bfa 50%, #c084fc 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.pf-hero__sub {
    font-size: 18px;
    line-height: 1.7;
    color: #71717a;
    max-width: 600px;
    margin: 0 auto 40px;
}
.pf-hero__sub strong { color: #a1a1aa; font-weight: 600; }
.pf-hero__cta { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
.pf-hero__stats {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 40px;
    margin-top: 36px;
    padding-top: 20px;
    border-top: 1px solid rgba(255 255 255 / .06);
    flex-wrap: wrap;
}
.pf-stat { text-align: center; }
.pf-stat__num { display: block; font-size: 28px; font-weight: 800; color: #fff; letter-spacing: -.03em; }
.pf-stat__label { display: block; font-size: 12px; color: #52525b; margin-top: 4px; font-weight: 500; }
.pf-stat__divider { width: 1px; height: 36px; background: rgba(255 255 255 / .06); }

/* ─── SECTION ─── */
.pf-section {
    max-width: 1200px;
    margin: 0 auto;
    padding: 48px 24px 72px;
    text-align: center;
}
.pf-section__label {
    font-size: 11px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase;
    color: #6366f1; margin-bottom: 16px;
}
.pf-section__title {
    font-size: clamp(28px, 4vw, 42px);
    font-weight: 800; letter-spacing: -.03em;
    color: #fff; margin: 0 0 16px;
}
.pf-section__sub { font-size: 16px; color: #52525b; max-width: 560px; margin: 0 auto 64px; line-height: 1.65; }

/* ─── DIAGRAM ─── */
.pf-diagram {
    border: 1px solid rgba(255 255 255 / .08);
    border-radius: 20px;
    overflow: hidden;
    background: rgba(255 255 255 / .02);
    backdrop-filter: blur(8px);
    text-align: left;
}
.pf-diagram__header {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    border-bottom: 1px solid rgba(255 255 255 / .08);
}
.pf-diagram__lane-title {
    padding: 14px 20px;
    font-size: 12px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase;
    display: flex; align-items: center; gap: 8px;
    border-right: 1px solid rgba(255 255 255 / .06);
}
.pf-diagram__lane-title:last-child { border-right: none; }
.pf-lane--blue { color: #818cf8; background: rgba(99,102,241,.06); }
.pf-lane--violet { color: #a78bfa; background: rgba(139,92,246,.06); }
.pf-lane--emerald { color: #34d399; background: rgba(16,185,129,.06); }

.pf-diagram__body {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    min-height: 480px;
}
.pf-lane {
    padding: 32px 20px;
    display: flex; flex-direction: column; align-items: center;
    border-right: 1px solid rgba(255 255 255 / .06);
    gap: 0;
}
.pf-lane:last-child { border-right: none; }
.pf-lane--blue-bg { background: rgba(99,102,241,.02); }
.pf-lane--violet-bg { background: rgba(139,92,246,.02); }
.pf-lane--emerald-bg { background: rgba(16,185,129,.02); }

.pf-lane__spacer { height: 80px; }
.pf-lane__spacer--lg { height: 220px; }

/* ─── NODES ─── */
.pf-node { display: flex; flex-direction: column; align-items: center; gap: 8px; }
.pf-node__label { font-size: 10px; font-weight: 600; color: #52525b; letter-spacing: .04em; text-transform: uppercase; }

.pf-node--start .pf-node__start-circle {
    width: 18px; height: 18px;
    background: #e4e4e7;
    border-radius: 50%;
    box-shadow: 0 0 0 3px rgba(228,228,231,.15);
}

.pf-node--end .pf-node__end-circle {
    width: 18px; height: 18px;
    border-radius: 50%;
    border: 3px solid #e4e4e7;
    background: transparent;
    box-shadow: 0 0 0 3px rgba(228,228,231,.15);
    position: relative;
}
.pf-node--end .pf-node__end-circle::after {
    content: '';
    position: absolute;
    inset: 3px;
    background: #e4e4e7;
    border-radius: 50%;
}

.pf-node--action {
    display: flex; align-items: center; gap: 10px;
    background: #18181b;
    border: 1px solid rgba(255 255 255 / .08);
    border-radius: 10px;
    padding: 12px 16px;
    font-size: 13px; font-weight: 500; color: #d4d4d8;
    width: 100%; max-width: 200px;
    transition: border-color .2s, transform .2s;
    flex-direction: row;
}
.pf-node--action:hover { border-color: rgba(255 255 255 / .18); transform: translateY(-1px); }
.pf-node--dashed { border-style: dashed; border-color: rgba(251,146,60,.3); background: rgba(251,146,60,.04); color: #fb923c; }
.pf-node--highlight { border-color: rgba(52,211,153,.3); background: rgba(16,185,129,.06); color: #34d399; }

.pf-node__icon {
    width: 26px; height: 26px;
    border-radius: 6px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.pf-node__icon--blue   { background: rgba(99,102,241,.15); color: #818cf8; }
.pf-node__icon--violet { background: rgba(139,92,246,.15); color: #a78bfa; }
.pf-node__icon--emerald{ background: rgba(16,185,129,.15); color: #34d399; }
.pf-node__icon--orange { background: rgba(251,146,60,.15); color: #fb923c; }

/* ─── DECISION ─── */
.pf-node--decision { width: 100%; display: flex; justify-content: center; }
.pf-decision-diamond {
    width: 80px; height: 80px;
    background: #18181b;
    border: 1px solid rgba(255 255 255 / .1);
    transform: rotate(45deg);
    display: flex; align-items: center; justify-content: center;
    border-radius: 6px;
}
.pf-decision-diamond span {
    transform: rotate(-45deg);
    font-size: 10px; font-weight: 700;
    color: #a78bfa; text-align: center;
    letter-spacing: .02em;
}
.pf-decision-branches {
    display: flex;
    width: 100%;
    justify-content: space-around;
    margin-top: 8px;
}
.pf-branch { display: flex; flex-direction: column; align-items: center; gap: 4px; }
.pf-branch__label {
    font-size: 10px; font-weight: 700;
    padding: 2px 8px; border-radius: 4px; letter-spacing: .04em;
}
.pf-branch__label--no   { color: #f87171; background: rgba(248,113,113,.12); border: 1px solid rgba(248,113,113,.2); }
.pf-branch__label--yes  { color: #34d399; background: rgba(52,211,153,.12); border: 1px solid rgba(52,211,153,.2); }

/* ─── ARROWS ─── */
.pf-arrow {
    width: 2px; height: 28px;
    background: linear-gradient(180deg, rgba(255 255 255 / .12) 0%, rgba(255 255 255 / .04) 100%);
    position: relative;
    margin: 2px auto;
}
.pf-arrow::after {
    content: '';
    position: absolute;
    bottom: -1px; left: 50%;
    transform: translateX(-50%);
    border: 4px solid transparent;
    border-top-color: rgba(255 255 255 / .2);
    border-right: none; border-left: none;
    border-bottom: none;
    width: 0; height: 0;
    border-left: 3px solid transparent;
    border-right: 3px solid transparent;
    border-top: 5px solid rgba(255 255 255 / .2);
}

/* ─── FEATURES ─── */
.pf-section--features { padding-top: 0; }
.pf-features {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 16px;
    text-align: left;
}
.pf-feature {
    background: rgba(255 255 255 / .02);
    border: 1px solid rgba(255 255 255 / .07);
    border-radius: 16px;
    padding: 28px;
    transition: border-color .2s, background .2s, transform .2s;
}
.pf-feature:hover {
    border-color: rgba(255 255 255 / .14);
    background: rgba(255 255 255 / .04);
    transform: translateY(-3px);
}
.pf-feature__icon {
    width: 44px; height: 44px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 20px;
}
.pf-feature__icon--blue   { background: rgba(99,102,241,.12); color: #818cf8; border: 1px solid rgba(99,102,241,.2); }
.pf-feature__icon--violet { background: rgba(139,92,246,.12); color: #a78bfa; border: 1px solid rgba(139,92,246,.2); }
.pf-feature__icon--emerald{ background: rgba(16,185,129,.12); color: #34d399; border: 1px solid rgba(16,185,129,.2); }
.pf-feature__icon--amber  { background: rgba(245,158,11,.12); color: #fbbf24; border: 1px solid rgba(245,158,11,.2); }
.pf-feature__icon--rose   { background: rgba(244,63,94,.12); color: #fb7185; border: 1px solid rgba(244,63,94,.2); }
.pf-feature__icon--cyan   { background: rgba(6,182,212,.12); color: #22d3ee; border: 1px solid rgba(6,182,212,.2); }
.pf-feature__title { font-size: 15px; font-weight: 700; color: #fff; margin: 0 0 10px; }
.pf-feature__desc { font-size: 14px; line-height: 1.65; color: #52525b; margin: 0; }

/* ─── FOOTER ─── */
.pf-footer {
    border-top: 1px solid rgba(255 255 255 / .06);
    padding: 36px 24px;
}
.pf-footer__inner {
    max-width: 1200px; margin: 0 auto;
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 16px;
}
.pf-footer__copy { font-size: 13px; color: #3f3f46; }
</style>
