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
from encuestas.sse_encuesta_curso ec
encuestas.sse_result_evaluacion_encuesta_curso reec
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



--Obtiene todos los registros que tiene acceso
select mact.modulo_padre_cve, mact.modulo_cve mod_hijo, mact.descripcion_modulo, mact.nom_controlador_funcion_mod
--, mract.role_id rol_id_hijo, mract.acceso acceso_hijo
, 1 acceso 
from encuestas.sse_modulo mact
left join encuestas.sse_modulo_rol mract on mract.modulo_cve = mact.modulo_cve and mract.role_id in (30, 1)
where mact.is_seccion = 0 and 
mact.modulo_padre_cve in (select mactp.modulo_cve
from encuestas.sse_modulo mactp
join encuestas.sse_modulo_rol mractp on mractp.modulo_cve = mactp.modulo_cve and mactp.is_seccion = 1 and mractp.role_id in (30, 1)
group by mactp.modulo_cve)
and mract.role_id is null 
group by mact.modulo_padre_cve, mact.modulo_cve--, mract.role_id, mract.acceso
;



select mact.modulo_cve, mact.modulo_padre_cve, mact.descripcion_modulo, 
mact.nom_controlador_funcion_mod,
		case when ((select count(*) cuenta
		--, mract.role_id
		from encuestas.sse_modulo_rol mract
		join encuestas.sse_modulo mact on mact.modulo_cve = mract.modulo_cve and acceso = 0 and mract.role_id in (30,1)
		group by mact.modulo_padre_cve, mact.modulo_cve
		--, mract.role_id
		having count(mact.modulo_cve) > 1) > 0) then 1
		--when 0 then 0
		else 0 end as acceso
--, mract.role_id
from encuestas.sse_modulo_rol mract
join encuestas.sse_modulo mact on mact.modulo_cve = mract.modulo_cve and acceso = 0 and mract.role_id in (30)
group by mact.modulo_padre_cve, mact.modulo_cve
--, mract.role_id
having count(mact.modulo_cve) = 1
;

select mact.modulo_cve, mact.modulo_padre_cve, mact.descripcion_modulo, 
mact.nom_controlador_funcion_mod, 0 acceso
--, mract.role_id
from encuestas.sse_modulo_rol mract
join encuestas.sse_modulo mact on mact.modulo_cve = mract.modulo_cve and acceso = 0 and mract.role_id in (30,1)
group by mact.modulo_padre_cve, mact.modulo_cve
--, mract.role_id
having count(mact.modulo_cve) > 1
;


--Obtener roles del usuario  admin: 31211, 43695, 34700 cn: 32077, 22615, 1423 CE:31138,10432,30104
--AGC29:488, 21951, 43909
SELECT mdl_role.id FROM mdl_course
    INNER JOIN mdl_context ON mdl_context.instanceid = mdl_course.id
    INNER JOIN mdl_role_assignments ON mdl_context.id = mdl_role_assignments.contextid
    INNER JOIN mdl_role ON mdl_role.id = mdl_role_assignments.roleid
    INNER JOIN mdl_user ON mdl_user.id = mdl_role_assignments.userid
    where mdl_user.id=31211
    group by mdl_role.id
     

