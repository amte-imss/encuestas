SELECT 
    c.name curso, c.short_name clave_curso,
    cc.modalidad, cc.tipo_cur, cc.tutorizado,
    r.name rol,
    
FROM encuestas.sse_ecuesta_curso ec
JOIN encuestas.sse_encuestas e ON(ec.encuesta_curso_cve = e.encuesta_cve)
JOIN encuestas.sse_preguntas p ON(e.encuesta_cve = p.encuesta_cve)
JOIN encuestas.sse_respuestas r ON(r.preguntas_cve = p.preguntas_cve AND r.encuesta_cve = p.encuesta_cve)
JOIN public.mdl_course_config cc ON(cc.course_id = ec.curso_cve)
JOIN public.mdl_course c ON(c.course_id = ec.curso_cve)
JOIN public.mdl_rol r ON(e.rol_id = r.rol_id)
WHERE 
    p.is_bono = 1
    AND ec.curso_cve = 1000 and ec.encuesta_curso_cve = 1