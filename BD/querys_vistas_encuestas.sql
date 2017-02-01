--Cálculo de promedios versión dos, incluye ponderación y "valido no aplica" además de is_bono
--Desc.-Cálculo de promedio de cursos por grupo, validado, validador, rol de validador y rol de validado
 select grupo_cve, evaluador, rol_evaluador, evaluado, rol_evaluado, sum(netos) as total, sum(no_puntua) as no_puntua_reg, 
 sum(nos_) total_no, sum(no_aplica_promedio) as total_no_aplica_cuenta_promedio, sum(puntua) as puntua_reg, 
 (sum(netos) - sum(no_puntua)) as base_reg,
(round(sum(puntua)::numeric * 100/(sum(netos) - sum(no_puntua))::numeric,3)) as porcentaje
from (
--Cuenta el total de respuestas en "Si" los que puntuan
select COUNT(res.texto) as puntua, 0 as no_puntua, 0 as netos, ev.group_id "grupo_cve", 0 as nos_, 0 as no_aplica_promedio,
ev.evaluador_user_cve "evaluador", ev.evaluador_rol_id "rol_evaluador", ev.evaluado_user_cve "evaluado", ev.evaluado_rol_id "rol_evaluado"
from encuestas.sse_evaluacion ev 
join encuestas.sse_respuestas res on res.reactivos_cve = ev.reactivos_cve
join encuestas.sse_preguntas pre on pre.preguntas_cve = ev.preguntas_cve
join encuestas.sse_encuesta_curso encc on encc.encuesta_cve = res.encuesta_cve    
where encc.course_cve = 822 and pre.is_bono = 1 and res.texto in('Si', 'Casi siempre', 'Siempre') 
group by ev.group_id, ev.evaluador_user_cve, ev.evaluador_rol_id, ev.evaluado_user_cve, ev.evaluado_rol_id
union
--Cuenta el total de base, que aplican para bono  
select 0 as puntua, 0 as no_puntua, COUNT(res.texto) as netos, ev.group_id "grupo_cve", 0 as nos_, 0 as no_aplica_promedio,
ev.evaluador_user_cve "evaluador", ev.evaluador_rol_id "rol_evaluador", ev.evaluado_user_cve "evaluado", ev.evaluado_rol_id "rol_evaluado"
from encuestas.sse_evaluacion ev 
join encuestas.sse_respuestas res on res.reactivos_cve = ev.reactivos_cve
join encuestas.sse_preguntas pre on pre.preguntas_cve = ev.preguntas_cve
join encuestas.sse_encuesta_curso encc on encc.encuesta_cve = res.encuesta_cve    
where encc.course_cve = 822  and pre.is_bono = 1
group by ev.group_id, ev.evaluador_user_cve, ev.evaluador_rol_id, ev.evaluado_user_cve, ev.evaluado_rol_id
union
--se obtiene el total de "no aplica" y que es valido para no aplica No puntua
select 0 as puntua, COUNT(res.texto) as no_puntua, 0 as netos, ev.group_id "grupo_cve", 0 as nos_, 0 as no_aplica_promedio,
ev.evaluador_user_cve "evaluador", ev.evaluador_rol_id "rol_evaluador", ev.evaluado_user_cve "evaluado", ev.evaluado_rol_id "rol_evaluado"
from encuestas.sse_evaluacion ev 
join encuestas.sse_respuestas res on res.reactivos_cve = ev.reactivos_cve
join encuestas.sse_preguntas pre on pre.preguntas_cve = ev.preguntas_cve
join encuestas.sse_encuesta_curso encc on encc.encuesta_cve = res.encuesta_cve    
where encc.course_cve = 822  and pre.is_bono = 1 and pre.valido_no_aplica = 1 
and res.texto in('No aplica', 'No envió mensaje')
group by ev.group_id, ev.evaluador_user_cve, ev.evaluador_rol_id, ev.evaluado_user_cve, ev.evaluado_rol_id
union
--Cuenta el total de respuestas en "No" 
select 0 as puntua, 0 as no_puntua, 0 as netos, ev.group_id "grupo_cve", COUNT(res.texto) as nos_, 0 as no_aplica_promedio,
ev.evaluador_user_cve "evaluador", ev.evaluador_rol_id "rol_evaluador", ev.evaluado_user_cve "evaluado", ev.evaluado_rol_id "rol_evaluado"
from encuestas.sse_evaluacion ev 
join encuestas.sse_respuestas res on res.reactivos_cve = ev.reactivos_cve
join encuestas.sse_preguntas pre on pre.preguntas_cve = ev.preguntas_cve
join encuestas.sse_encuesta_curso encc on encc.encuesta_cve = res.encuesta_cve    
where encc.course_cve = 822 and pre.is_bono = 1 and res.texto in('No', 'Casi nunca', 'Nunca', 'Algunas veces') 
group by ev.group_id, ev.evaluador_user_cve, ev.evaluador_rol_id, ev.evaluado_user_cve, ev.evaluado_rol_id
union
--Cuenta el total de respuestas en "No aplica" y "No envió mensaje" que se contabilizan para el promedio 
select 0 as puntua, 0 as no_puntua, 0 as netos, ev.group_id "grupo_cve", 0 as nos_, COUNT(res.texto) as no_aplica_promedio,
ev.evaluador_user_cve "evaluador", ev.evaluador_rol_id "rol_evaluador", ev.evaluado_user_cve "evaluado", ev.evaluado_rol_id "rol_evaluado"
from encuestas.sse_evaluacion ev 
join encuestas.sse_respuestas res on res.reactivos_cve = ev.reactivos_cve
join encuestas.sse_preguntas pre on pre.preguntas_cve = ev.preguntas_cve
join encuestas.sse_encuesta_curso encc on encc.encuesta_cve = res.encuesta_cve    
where encc.course_cve = 822  and pre.is_bono = 1 and pre.valido_no_aplica = 0 
and res.texto in('No aplica', 'No envió mensaje')
group by ev.group_id, ev.evaluador_user_cve, ev.evaluador_rol_id, ev.evaluado_user_cve, ev.evaluado_rol_id
) as calculos_promedio
group by grupo_cve, evaluador, rol_evaluador, evaluado, rol_evaluado
;



---Cálculo de promedios versión dos, incluye ponderación y valido no aplica además de is bono
---Obtener el promedio total de las encuestas contestadas de un curso y por grupo
select grupo_cve, sum(netos) as total, sum(no_puntua) as no_puntua_reg, sum(puntua) as puntua_reg, 
(sum(netos) - sum(no_puntua)) as base_reg,
(round(sum(puntua)::numeric * 100/(sum(netos) - sum(no_puntua))::numeric,3)) as porcentaje
from (
--Cuenta el total de respuestas en "Si" los que puntuan
select COUNT(res.texto) as puntua, 0 as no_puntua, 0 as netos, ev.group_id "grupo_cve"
from encuestas.sse_evaluacion ev 
join encuestas.sse_respuestas res on res.reactivos_cve = ev.reactivos_cve
join encuestas.sse_preguntas pre on pre.preguntas_cve = ev.preguntas_cve
join encuestas.sse_encuesta_curso encc on encc.encuesta_cve = res.encuesta_cve    
where encc.course_cve = 822 and pre.is_bono = 1 and res.texto in('Si') 
group by ev.group_id
union
--Cuenta el total de base, que aplican para bono  
select 0 as puntua, 0 as no_puntua, COUNT(res.texto) as netos, ev.group_id "grupo_cve"
from encuestas.sse_evaluacion ev 
join encuestas.sse_respuestas res on res.reactivos_cve = ev.reactivos_cve
join encuestas.sse_preguntas pre on pre.preguntas_cve = ev.preguntas_cve
join encuestas.sse_encuesta_curso encc on encc.encuesta_cve = res.encuesta_cve    
where encc.course_cve = 822  and pre.is_bono = 1
group by ev.group_id
union
--se obtiene el total de "no aplica" y que es valido para no aplica
select 0 as puntua, COUNT(res.texto) as no_puntua, 0 as netos, ev.group_id "grupo_cve" 
from encuestas.sse_evaluacion ev 
join encuestas.sse_respuestas res on res.reactivos_cve = ev.reactivos_cve
join encuestas.sse_preguntas pre on pre.preguntas_cve = ev.preguntas_cve
join encuestas.sse_encuesta_curso encc on encc.encuesta_cve = res.encuesta_cve    
where encc.course_cve = 822  and pre.is_bono = 1 and pre.valido_no_aplica = 1 
and res.texto in('No aplica')
group by ev.group_id
) as calculos_promedio
group by grupo_cve
;

---Obtiene información de cursos asociados con usuario 
SELECT mco.shortname "clave_curso", mco.fullname, mr."name", mu.firstname, mu.lastname, mco.id "curso_id"
,mco.tipocur, mccg.tutorizado, mu.id "iduser"
FROM mdl_course mco
left join mdl_course_config mccg on mccg.course = mco.id
INNER JOIN mdl_context mctx ON mctx.instanceid = mco.id
INNER JOIN mdl_role_assignments mrass ON mctx.id = mrass.contextid
INNER JOIN mdl_role mr ON mr.id = mrass.roleid
INNER JOIN mdl_user mu on mu.id = mrass.userid
WHERE mr."name" like '%Alumno%' and mccg.tutorizado = 1;
mu.id=487;

---Obtiene reglas de evaluación de una encuestas, evaluador y evaluado 
select *, mrdo."name" , mrdor."name"  
 from encuestas.sse_encuesta_curso cce
 join encuestas.sse_encuestas enc on enc.encuesta_cve = cce.encuesta_cve
 join encuestas.sse_reglas_evaluacion re on re.reglas_evaluacion_cve = enc.reglas_evaluacion_cve
 left join mdl_role mrdor on mrdor.id = re.rol_evaluador_cve
 left join mdl_role mrdo on mrdo.id = re.rol_evaluado_cve 
 where cce.course_cve  = 838;


 --obtiene las reglas que podrian aplicar a la encuesta 
		WITH RECURSIVE busca_excepcion AS (
		SELECT reg.reglas_evaluacion_cve, reg.rol_evaluado_cve, reg.rol_evaluador_cve, 
		reg.is_excepcion, reg.tutorizado, reg.is_bono, reg.ord_prioridad
		FROM encuestas.sse_reglas_evaluacion reg
		JOIN encuestas.sse_encuestas enc ON enc.reglas_evaluacion_cve=reg.reglas_evaluacion_cve
		JOIN encuestas.sse_encuesta_curso encc ON encc.encuesta_cve=enc.encuesta_cve 
				WHERE reg.reglas_evaluacion_cve = 10
				 UNION all 
		select bex.is_excepcion, rer.rol_evaluado_cve, rer.rol_evaluador_cve, 
		rer.is_excepcion, rer.tutorizado, rer.is_bono, rer.ord_prioridad 
		from busca_excepcion bex
		join encuestas.sse_reglas_evaluacion rer on rer.reglas_evaluacion_cve = bex.is_excepcion
		)
		select * FROM busca_excepcion OFFSET 0 
			
 --obtiene las reglas que podrian aplicar a la encuesta 
 WITH RECURSIVE busca_excepcion AS (
SELECT reg.reglas_evaluacion_cve, reg.rol_evaluado_cve, reg.rol_evaluador_cve, reg.is_excepcion, reg.tutorizado, reg.is_bono, reg.ord_prioridad
FROM encuestas.sse_reglas_evaluacion reg
JOIN encuestas.sse_encuestas enc ON enc.reglas_evaluacion_cve=reg.reglas_evaluacion_cve
JOIN encuestas.sse_encuesta_curso encc ON encc.encuesta_cve=enc.encuesta_cve
WHERE reg.rol_evaluador_cve = 5 AND reg.tutorizado = 1 AND encc.course_cve = 838  
UNION all 
select bex.is_excepcion, rer.rol_evaluado_cve, rer.rol_evaluador_cve, rer.is_excepcion, rer.tutorizado, rer.is_bono, rer.ord_prioridad 
from busca_excepcion bex
join encuestas.sse_reglas_evaluacion rer on rer.reglas_evaluacion_cve = bex.is_excepcion
)
select * FROM busca_excepcion OFFSET 0;

 
--Vista para obtener información del curso especifica
drop view encuestas.view_datos_curso;
create view encuestas.view_datos_curso as
select cur.id as idc, shortname as clave, fullname as namec,
CASE ccfg.tipocur 
WHEN 0 THEN CASE SUBSTRING(cur.shortname from '%#\"GPC#\"%' FOR '#')  
WHEN 'GPC' THEN 'Curso basado en GPC' 
ELSE 'Curso' END
WHEN 1 THEN 'Diplomado' 
ELSE 'Error'  
END tipo_curso,
CASE ccfg.tipocur 
WHEN 0 THEN CASE SUBSTRING(cur.shortname from '%#\"GPC#\"%' FOR '#')  
WHEN 'GPC' THEN 3 
ELSE 2 end 
WHEN 1 THEN 1 
ELSE 0  
END as tipo_curso_id,
(encuestas.gettutorizado_notutorizado(shortname)) as tutorizado_anterir,
ccfg.tutorizado,
CASE ccfg.tutorizado 
WHEN 0 THEN 'No tutorizado'
WHEN 1 THEN 'Tutorizado' 
ELSE 'ND'  
END as tex_tutorizado,
ccfg.horascur,
TO_CHAR(TO_TIMESTAMP(cur.startdate),'YYYY') anio,
--bono
ccfg.lastdate fecha_fin,
DATE(TO_CHAR(TO_TIMESTAMP(cur.startdate),'YYYY-MM-DD')) fecha_inicio,
CASE ccfg.tipocur 
WHEN 0 THEN 1
WHEN 1 THEN 3 
ELSE 0  
END alcance_curso,
CASE 
WHEN (ccfg.horascur > 120) THEN 6
WHEN (ccfg.horascur > 80 AND ccfg.horascur <= 120) THEN 3
WHEN (ccfg.horascur >= 40 AND ccfg.horascur <= 80) THEN 2
WHEN (ccfg.horascur < 40 AND ccfg.horascur > 0) THEN 1
ELSE 0
END puntaje_duracion
from mdl_course cur 
join mdl_course_config ccfg ON(ccfg.course = cur.id);




--Consulta general para obtener datos del curso y el rol a partir de la vista de cursos
drop view encuestas.view_datos_usuario;
create view encuestas.view_datos_usuario as
 SELECT 
--usuerio
u.id AS iduser, u.username AS nom_usuario, u.firstname,  u.lastname,
--Tutor
tutor.cve_departamento,
cat.cve_categoria,
cat.nom_nombre categoria,
tutor.num_ant_anio,
tutor.num_ant_quincena,
tutor.num_ant_dias,
tutor.fch_pre_registro,	
--curso
vcg.idc,
vcg.clave,
vcg.namec,
vcg.fecha_inicio,
vcg.fecha_fin,
vcg.tipo_curso_id,
vcg.tipo_curso,
vcg.tutorizado_anterir,
vcg.tutorizado,
vcg.tex_tutorizado,
vcg.horascur,
vcg.anio,
--Rol	
r.id rol_id,
r.name rol,
(select min(d.cve_delegacion) from departments.ssv_departamentos d
where d.cve_depto_adscripcion = tutor.cve_departamento) as del_cve,
(select min(d.nom_delegacion) from departments.ssv_departamentos d
where d.cve_depto_adscripcion = tutor.cve_departamento) as del_name
FROM  mdl_role_assignments ra 
JOIN public.mdl_user u ON ra.userid = u.id
JOIN mdl_role r on (r.id = ra.roleid AND r.id IN (14,18,32,33,30))
JOIN mdl_context ct ON ct.id = ra.contextid
left join encuestas.view_datos_curso vcg on (vcg.idc = ct.instanceid)
LEFT JOIN tutorias.mdl_usertutor tutor ON(u.username = tutor.nom_usuario)
LEFT JOIN nomina.ssn_categoria cat ON(cat.cve_categoria = tutor.cve_categoria)
;
r.id= 5
WHERE 
TO_CHAR(vcg.fecha_inicio,'YYYY') = '2015' 
and u.id = 1423
and tutor.fch_pre_registro = '2010-01-07'
vcg.anio = '2016'


 --Consulta general para obtener datos del curso y el rol
 SELECT 
--usuerio
u.id AS iduser, u.username AS nom_usuario, u.firstname,  u.lastname,
--nomina
--CURP	
--Fecha de Nacimiento	
--Sexo	
--Fecha de ingreso al IMSS	
--RFC
--Num. de red	
--Teléfono particular	
--tutorias
tutor.emailpart coreo_personal,
tutor.emaillab correo_institucional,
tutor.cve_departamento,
cat.cve_categoria,
cat.nom_nombre categoria,
tutor.num_ant_anio,
tutor.num_ant_quincena,
tutor.num_ant_dias,
tutor.fch_pre_registro,	
--curso
cur.id curso_id,
cur.shortname clave_curso,
cur.fullname curso,
DATE(TO_CHAR(TO_TIMESTAMP(cur.startdate),'YYYY-MM-DD')) fecha_inicio,
ccfg.lastdate fecha_fin,
CASE ccfg.tipocur 
WHEN 0 THEN CASE SUBSTRING(cur.shortname from '%#\"GPC#\"%' FOR '#')  
WHEN 'GPC' THEN 'Curso basado en GPC' 
ELSE 'Curso' 
END
WHEN 1 THEN 'Diplomado' 
ELSE 'Error'  
END tipo_curso,
ccfg.horascur,
TO_CHAR(TO_TIMESTAMP(cur.startdate),'YYYY') anio,
--Rol	
r.id rol_id,
r.name rol,
--bono
CASE ccfg.tipocur 
WHEN 0 THEN 1
WHEN 1 THEN 3 
ELSE 0  
END alcance_curso,
CASE 
WHEN (ccfg.horascur > 120) THEN 6
WHEN (ccfg.horascur > 80 AND ccfg.horascur <= 120) THEN 3
WHEN (ccfg.horascur >= 40 AND ccfg.horascur <= 80) THEN 2
WHEN (ccfg.horascur < 40 AND ccfg.horascur > 0) THEN 1
ELSE 0
END puntaje_duracion
FROM  mdl_role_assignments ra 
JOIN public.mdl_user u ON ra.userid = u.id
JOIN mdl_role r on (r.id = ra.roleid AND r.id IN (14,18,32,33))
JOIN mdl_context ct ON ct.id = ra.contextid
JOIN mdl_course cur ON(ct.instanceid = cur.id)
JOIN mdl_course_config ccfg ON(ccfg.course = cur.id)
LEFT JOIN tutorias.mdl_usertutor tutor ON(u.username = tutor.nom_usuario)
LEFT JOIN nomina.ssn_categoria cat ON(cat.cve_categoria = tutor.cve_categoria)
WHERE TO_CHAR(TO_TIMESTAMP(cur.startdate),'YYYY') = '2015'
order by anio;

 
 
CREATE OR REPLACE FUNCTION encuestas.get_yes_not_tutorizado(clave_curso character varying) RETURNS character varying AS $f$
DECLARE	lst_row RECORD;	
DECLARE texto_a varchar;
--DECLARE texto_b varchar;
BEGIN
	texto_a := '';
	--texto_b := '';
	FOR lst_row IN 
	SELECT DISTINCT r.id 
	--INTO lst_row 
	FROM  mdl_role_assignments ra JOIN public.mdl_role r ON(r.id = ra.roleid AND r.id IN (14,18,32,33))
	JOIN mdl_context ct ON ct.id = ra.contextid	JOIN mdl_course cur ON ct.instanceid = cur.id
	WHERE cur.shortname = clave_curso ORDER BY r.id
	LOOP
	  texto_a := texto_a || lst_row.id;
	  --NEXT lst_row;
	END LOOP;
	
	CASE 
		WHEN texto_a = '' THEN 
			texto_a := 'Otro';
		WHEN texto_a = '14' THEN 
			texto_a := 'No_Tutorizado';
		ELSE 
		texto_a :=  'Tutorizado';
	END CASE; 
	
	RETURN texto_a;
END;
$f$
  LANGUAGE plpgsql       

--Dar un valor a un curso tutorizado y no tutorizado 0 = No tutorizdo; 1 = Totorizado; -1 = No definido     
CREATE OR REPLACE FUNCTION encuestas.gettutorizado_notutorizado(clave_curso character varying) RETURNS character varying AS $f$
DECLARE	lst_row RECORD;	
DECLARE texto_a varchar;
--DECLARE texto_b varchar;
BEGIN
	texto_a := '';
	--texto_b := '';
	FOR lst_row IN 
	SELECT DISTINCT r.id 
	--INTO lst_row 
	FROM  mdl_role_assignments ra JOIN public.mdl_role r ON(r.id = ra.roleid AND r.id IN (14,18,32,33))
	JOIN mdl_context ct ON ct.id = ra.contextid	JOIN mdl_course cur ON ct.instanceid = cur.id
	WHERE cur.shortname = clave_curso ORDER BY r.id
	LOOP
	  texto_a := texto_a || lst_row.id;
	  --NEXT lst_row;
	END LOOP;
	
		CASE 
		WHEN texto_a = '' THEN 
			texto_a := -1;
		WHEN texto_a = '14' THEN 
			texto_a := 0;
		ELSE 
		texto_a :=  1;
	END CASE;  
	
	RETURN texto_a;
END;
$f$
  LANGUAGE plpgsql         
 
 ----Pruebas para saber que un curso es tutorizado, ejemplo para validar que es tutorizado
 SELECT DISTINCT r.id 
	--INTO lst_row 
	FROM  mdl_role_assignments ra JOIN public.mdl_role r ON(r.id = ra.roleid AND r.id IN (14,18,32,33))
	JOIN mdl_context ct ON ct.id = ra.contextid	JOIN mdl_course cur ON ct.instanceid = cur.id
	WHERE cur.shortname = 'CES-SPE-F1-10' 
	ORDER BY r.id;
	
select * from encuestas.get_yes_not_tutorizado('CES-SPE-F1-10');


---Bonos detalle de bonos Jesús Días
select enc.encuesta_cve, enc.descripcion_encuestas, enc.is_bono, enc.tipo_encuesta, enc.eva_tipo, tex_tutorizado,
	eva.course_cve, curso.namec, curso.clave, curso.tipo_curso, curso.tipo_curso_id, curso.horascur, curso.anio, curso.fecha_inicio, curso.fecha_fin, eva.group_id, grupo.name, 
	eva.evaluado_user_cve, eva.evaluado_rol_id, rol_evaluado.name, 
		tut_evaluado.cve_departamento, (select * from departments.get_rama_completa(tut_evaluado.cve_departamento, 7)) as rama_tut_evaluado, 
		evaluado.cve_departamental, (select * from departments.get_rama_completa(evaluado.cve_departamental, 7)) as rama_evaluado, 
		tut_evaluado.cve_categoria, cat_tut_evaluado.nom_nombre, evaluado.cat, cat_evaluado.nom_nombre, 
	eva.evaluador_user_cve, eva.evaluador_rol_id, rol_evaluador.name, evaluador.cve_departamental, evaluador.cat,
	evaluado.username, evaluado.firstname, evaluado.lastname, evaluador.username, evaluador.firstname, evaluador.lastname,
	enc.reglas_evaluacion_cve/*, eva.preguntas_cve, eva.reactivos_cve*/
from encuestas.sse_encuestas enc
inner join encuestas.sse_evaluacion eva on eva.encuesta_cve=enc.encuesta_cve
inner join encuestas.view_datos_curso curso on curso.idc=eva.course_cve
left join encuestas.sse_reglas_evaluacion eva_reg on eva_reg.reglas_evaluacion_cve=enc.reglas_evaluacion_cve
left join public.mdl_groups grupo ON grupo.id=eva.group_id
left join public.mdl_user evaluado on evaluado.id=eva.evaluado_user_cve
left join public.mdl_role rol_evaluado on rol_evaluado.id=eva.evaluado_rol_id
left join tutorias.mdl_usertutor tut_evaluado on tut_evaluado.nom_usuario=evaluado.username and tut_evaluado.id_curso=eva.course_cve 
	and eva.evaluado_rol_id <> 5
left join nomina.ssn_categoria cat_evaluado ON cat_evaluado.cve_categoria = evaluado.cat
left join nomina.ssn_categoria cat_tut_evaluado ON cat_tut_evaluado.cve_categoria = tut_evaluado.cve_categoria
--left join departments.ssd_cat_depto_adscripcion depto_evaluado ON depto_evaluado.cve_depto_adscripcion=tut_evaluado.cve_departamento 
--left join encuestas.view_datos_usuario tut_evaluador on tut_evaluador.nom_usuario=evaluado.username and tut_evaluador.idc=eva.course_cve  
left join public.mdl_user evaluador on evaluador.id=eva.evaluador_user_cve
left join public.mdl_role rol_evaluador on rol_evaluador.id=eva.evaluador_rol_id
left join gestion.sgp_tab_preregistro_al pre_evaluado on pre_evaluado.nom_usuario=evaluado.username and pre_evaluado.cve_curso=eva.course_cve 
	/*and eva.evaluado_rol_id = 5*/
where eva.course_cve=838
group by enc.encuesta_cve, enc.descripcion_encuestas, enc.is_bono, enc.reglas_evaluacion_cve, enc.tipo_encuesta, enc.eva_tipo, tex_tutorizado,
	eva.course_cve, curso.namec, curso.clave, curso.tipo_curso, curso.tipo_curso_id, curso.horascur, curso.anio, curso.fecha_inicio, curso.fecha_fin, eva.group_id, grupo.name, 
	eva.evaluado_user_cve, eva.evaluado_rol_id, rol_evaluado.name, tut_evaluado.cve_departamento, evaluado.cve_departamental, tut_evaluado.cve_categoria, cat_tut_evaluado.nom_nombre, evaluado.cat, cat_evaluado.nom_nombre,  
	eva.evaluador_user_cve, eva.evaluador_rol_id, rol_evaluador.name, evaluador.cve_departamental, evaluador.cat,
	evaluado.username, evaluado.firstname, evaluado.lastname, evaluador.username, evaluador.firstname, evaluador.lastname,
	enc.reglas_evaluacion_cve;
 
 
 ----Bonos Reporte de implementación
 SELECT eeec.course_cve, eeec.evaluado_user_cve, vdc.clave, vdc.namec, vdc.tex_tutorizado, vdc.tipo_curso, concat(evaluado.firstname, ' ', evaluado.lastname) as name_evaluador, evaluado.username, revaluado.name as name_rol_evaluado, revaluado.id id_rol_evaluado
FROM encuestas.sse_result_evaluacion_encuesta_curso eeec
JOIN encuestas.view_datos_curso vdc ON vdc.idc = eeec.course_cve
LEFT JOIN public.mdl_user evaluado ON evaluado.id = eeec.evaluado_user_cve
JOIN encuestas.sse_encuestas enc ON enc.encuesta_cve = eeec.encuesta_cve
JOIN encuestas.sse_reglas_evaluacion reg ON reg.reglas_evaluacion_cve = enc.reglas_evaluacion_cve
LEFT JOIN public.mdl_role revaluado ON revaluado.id=reg.rol_evaluado_cve
WHERE revaluado.id = '32'
GROUP BY eeec.course_cve, vdc.clave, vdc.namec, evaluado_user_cve, vdc.tex_tutorizado, vdc.tipo_curso, evaluado.firstname, evaluado.lastname, evaluado.username, revaluado.name, revaluado.id
 LIMIT 5
 
 
 --Encuestas contestadas LEAS 25012017
 select 
ec.course_cve, ccfg.tutorizado, reec.encuesta_cve, reec.evaluador_user_cve, reec.evaluado_user_cve, 
mg."name" name_grupo,
(select string_agg(mgs."name", ', ' order by mgs."name") from public.mdl_groups mgs where mgs.id = any (string_to_array(reec.grupos_ids_text, ',')::int8[])) as name_grupos,
--evaluado
mrdo.id rid_do, mrdo."name" rolname_do, uedo.username as matricula_do, concat(uedo.nom, ' ', uedo.pat, ' ', uedo.mat) nom_evaluado 
,cattutdo.des_clave, cattutdo.nom_nombre,
concat(depdo.cve_depto_adscripcion, ' - ', depdo.des_unidad_atencion) depart_do, depdor.nom_delegacion del_do, depdo.name_region reg_do,  
cattutdor.des_clave clave_cattut_do, cattutdor.nom_nombre name_cattut_do,
--evaluador
mrdor.id rid_dor, mrdor."name" rolname_dor, uedor.username as matricula_dor,  concat(uedor.nom, ' ', uedor.pat, ' ', uedor.mat) nom_evaluador,
cattutdor.des_clave clave_cattut_dor, cattutdor.nom_nombre name_cattut_dor, catpredor.des_clave clave_catpre_dor, catpredor.nom_nombre name_catpre_dor,
concat(depdor.cve_depto_adscripcion, ' - ', depdor.des_unidad_atencion) depart_dor, depdor.nom_delegacion delegacion_dor, depdor.name_region reg_dor,
concat(deppredor.cve_depto_adscripcion, ' - ', deppredor.des_unidad_atencion) departpre_dor, deppredor.nom_delegacion delpre_dor, deppredor.name_region regpre_dor,
reec.calif_emitida, reec.calif_emitida_napb, reec.group_id, grupos_ids_text
--,cbg.bloque
from encuestas.sse_result_evaluacion_encuesta_curso reec
join encuestas.sse_encuesta_curso ec on reec.encuesta_cve = ec.encuesta_cve and reec.course_cve = ec.course_cve
join public.mdl_groups mg on mg.id = reec.group_id or mg.id = ANY (string_to_array(reec.grupos_ids_text, ',')::int[])
join public.mdl_course mcs on mcs.id = ec.course_cve
join public.mdl_course_config ccfg on ccfg.course = mcs.id
join encuestas.sse_encuestas enc on enc.encuesta_cve = reec.encuesta_cve
join encuestas.sse_reglas_evaluacion rege on rege.reglas_evaluacion_cve = enc.reglas_evaluacion_cve
--Evaluador
join public.mdl_user uedor on uedor.id = reec.evaluador_user_cve
join public.mdl_role mrdor on mrdor.id = rege.rol_evaluador_cve
left join gestion.sgp_tab_preregistro_al gpregdor on gpregdor.nom_usuario = uedor.username and gpregdor.cve_curso = ec.course_cve and rege.rol_evaluador_cve = 5 
LEFT JOIN nomina.ssn_categoria catpredor ON catpredor.cve_categoria = gpregdor.cve_cat
left join departments.ssv_departamentos deppredor on deppredor.cve_depto_adscripcion = gpregdor.cve_departamental
LEFT JOIN tutorias.mdl_usertutor tutdor ON tutdor.nom_usuario=uedor.username and tutdor.id_curso=ec.course_cve
LEFT JOIN nomina.ssn_categoria cattutdor ON cattutdor.cve_categoria = tutdor.cve_categoria
left join departments.ssv_departamentos depdor on depdor.cve_depto_adscripcion = tutdor.cve_departamento
--Evaluado 
join public.mdl_user uedo on uedo.id = reec.evaluado_user_cve
join public.mdl_role mrdo on mrdo.id = rege.rol_evaluado_cve 
LEFT JOIN tutorias.mdl_usertutor tutdo ON tutdo.nom_usuario=uedo.username and tutdo.id_curso=ec.course_cve
LEFT JOIN nomina.ssn_categoria cattutdo ON cattutdo.cve_categoria = tutdo.cve_categoria
left join departments.ssv_departamentos depdo on depdo.cve_depto_adscripcion = tutdo.cve_departamento
--join encuestas.sse_curso_bloque_grupo cbg on cbg.course_cve = reec.course_cve and (cbg.mdl_groups_cve = reec.group_id )--or cbg.mdl_groups_cve = ANY (string_to_array(reec.grupos_ids_text, ',')::int[]))
where ccfg.tutorizado = 0 --and rege.rol_evaluado_cve = 32 
--and lower(translate(concat(uedo.nom, ' ',uedo.pat, ' ', uedo.mat),'áéíóúÁÉÍÓÚüÜ','aeiouAEIOUuU')) like lower(translate('%Fatima Korina Gaytán Núñez%','áéíóúÁÉÍÓÚüÜ','aeiouAEIOUuU')) 
group by ec.course_cve, mcs.shortname, ccfg.tutorizado, reec.encuesta_cve, reec.evaluador_user_cve, reec.evaluado_user_cve, mg."name",
--evaluado
mrdo.id, mrdo."name", uedo.username, uedo.nom, uedo.pat, uedo.mat,
depdo.cve_depto_adscripcion, depdo.des_unidad_atencion, depdo.nom_delegacion, depdo.name_region,
cattutdor.des_clave, cattutdor.nom_nombre, 
--evaluador
mrdor.id, mrdor."name", uedor.username, uedor.nom, uedor.pat, uedor.mat,
cattutdor.des_clave, cattutdor.nom_nombre, catpredor.des_clave, catpredor.nom_nombre,
depdor.cve_depto_adscripcion, depdor.des_unidad_atencion, depdor.nom_delegacion, depdor.name_region,
deppredor.cve_depto_adscripcion, deppredor.des_unidad_atencion,  deppredor.nom_delegacion, deppredor.name_region, 
--más
reec.calif_emitida, reec.calif_emitida_napb, reec.group_id, grupos_ids_text
,cattutdo.des_clave, cattutdo.nom_nombre 
--,cbg.bloque
 