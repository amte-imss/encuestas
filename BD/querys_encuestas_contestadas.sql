--
--Encuestas contestadas LEAS 26/enero/2017
--
select 
ec.course_cve, ccfg.tutorizado, reec.encuesta_cve, reec.evaluador_user_cve, reec.evaluado_user_cve, 
mg."name" name_grupo,
(select string_agg(mgs."name", ', ' order by mgs."name") from public.mdl_groups mgs where mgs.id = any (string_to_array(reec.grupos_ids_text, ',')::int8[])) as name_grupos,
--evaluado
mrdo.id rid_do, mrdo."name" rolname_do, uedo.username as matricula_do, uedo.id userid_do, concat(uedo.nom, ' ', uedo.pat, ' ', uedo.mat) nom_evaluado 
,cattutdo.des_clave, cattutdo.nom_nombre,
concat(depdo.cve_depto_adscripcion, ' - ', depdo.des_unidad_atencion) depart_do, depdor.nom_delegacion del_do, depdo.name_region reg_do,  
cattutdor.des_clave clave_cattut_do, cattutdor.nom_nombre name_cattut_do,
--evaluador
mrdor.id rid_dor, mrdor."name" rolname_dor, uedor.username as matricula_dor, uedor.id userid_dor, concat(uedor.nom, ' ', uedor.pat, ' ', uedor.mat) nom_evaluador,
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
where ccfg.tutorizado = 1 --and rege.rol_evaluado_cve = 32 
--and lower(translate(concat(uedo.nom, ' ',uedo.pat, ' ', uedo.mat),'áéíóúÁÉÍÓÚüÜ','aeiouAEIOUuU')) like lower(translate('%Fatima Korina Gaytán Núñez%','áéíóúÁÉÍÓÚüÜ','aeiouAEIOUuU')) 
group by ec.course_cve, mcs.shortname, ccfg.tutorizado, reec.encuesta_cve, reec.evaluador_user_cve, reec.evaluado_user_cve, mg."name",
--evaluado
mrdo.id, mrdo."name", uedo.id, uedo.username, uedo.nom, uedo.pat, uedo.mat,
depdo.cve_depto_adscripcion, depdo.des_unidad_atencion, depdo.nom_delegacion, depdo.name_region,
cattutdor.des_clave, cattutdor.nom_nombre, 
--evaluador
mrdor.id, mrdor."name", uedor.id, uedor.username, uedor.nom, uedor.pat, uedor.mat,
cattutdor.des_clave, cattutdor.nom_nombre, catpredor.des_clave, catpredor.nom_nombre,
depdor.cve_depto_adscripcion, depdor.des_unidad_atencion, depdor.nom_delegacion, depdor.name_region,
deppredor.cve_depto_adscripcion, deppredor.des_unidad_atencion,  deppredor.nom_delegacion, deppredor.name_region, 
--más
reec.calif_emitida, reec.calif_emitida_napb, reec.group_id, grupos_ids_text
,cattutdo.des_clave, cattutdo.nom_nombre 
--,cbg.bloque



--
--Encuestas contestadas LEAS 26/enero/2017
--