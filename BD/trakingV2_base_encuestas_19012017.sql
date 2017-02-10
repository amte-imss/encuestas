/* funcion almacenada para facilitar el conteo de indicadores */
drop function if exists encuestas.get_value_reactivo(numeric, numeric, character varying);


create function encuestas.get_value_reactivo(tipo_conteo numeric, valido_no_aplica numeric, respuesta character varying) returns int 
as $$
declare salida smallint;
begin
	salida:=0;
	if tipo_conteo = 1 and lower(respuesta) in  ('si', 'casi siempre','siempre') then
		salida:=1;
	else if tipo_conteo = 1 and lower(respuesta) in ('no','casi nunca','nunca','algunas veces') then 
		salida:=0;
	else if tipo_conteo = 2 and lower(respuesta) in ('si', 'casi siempre','siempre') then
		salida:=0;
	else if tipo_conteo = 2 and lower(respuesta) in ('no','casi nunca','nunca','algunas veces') then
		salida:=1;
	end if;
	end if;
	end if;
	end if;
	
	if tipo_conteo = 1 and valido_no_aplica = 1 and lower(respuesta) in ('no aplica', 'no envió mensaje') then
		salida:=1;
	else if tipo_conteo = 3 and valido_no_aplica != 1 and lower(respuesta) in ('no aplica', 'no envió mensaje') then
		salida:=1;
	end if;
	end if;
	return salida;
end;
$$ LANGUAGE plpgsql;

--
-- Modifica entidad que almacena el promedio de las encuestas para guardar encuestas y preguntas que no aplican para bono
--  Ejecución LEAS 18/01/2017
alter table encuestas.sse_result_evaluacion_encuesta_curso add column total_puntua_si_napb int4 NOT NULL DEFAULT 0;
alter table encuestas.sse_result_evaluacion_encuesta_curso add column total_nos_napb int4 NOT NULL DEFAULT 0;
alter table encuestas.sse_result_evaluacion_encuesta_curso add column total_no_puntua_napv_napb int4 NOT NULL DEFAULT 0;-- Los "no aplica" que no forman parte de la evaluación de la encuesta, es decir, no_aplican = 1
alter table encuestas.sse_result_evaluacion_encuesta_curso add column base_napb int4 NOT NULL DEFAULT 0;
alter table encuestas.sse_result_evaluacion_encuesta_curso add column calif_emitida_napb numeric(6,3) NOT NULL DEFAULT 0;
alter table encuestas.sse_result_evaluacion_encuesta_curso add column total_reactivos_napb int4 NOT NULL DEFAULT 0;
alter table encuestas.sse_result_evaluacion_encuesta_curso add column total_no_puntua_apv int4 NOT NULL DEFAULT 0; -- Los "no aplica" que forman parte de la evaluación de la encuesta, es decir, no_aplican = 0
alter table encuestas.sse_result_evaluacion_encuesta_curso add column total_no_puntua_apv_napb int4 NOT NULL DEFAULT 0 ; -- Los "no aplica" que forman parte de la evaluación de la encuesta, es decir, no_aplican = 0

--
-- Modifica default de colmna del campo orden de priorida en las reglas de evaluación
--   Ejecución LEAS 23/01/2017
ALTER TABLE encuestas.sse_reglas_evaluacion ALTER COLUMN ord_prioridad SET DEFAULT 1;

--
 --Crear tablas para guardar designación de evaluación de autoevaluación
 -- Ejecución Jesús 03/02/2017 falta agregar--------------------**********************************************************

--
--Crear tablas para manejo de privilegios de rol
-- Ejecución LEAS 02/02/2017
CREATE TABLE encuestas.sse_modulo (
	modulo_cve serial,
	descripcion_modulo varchar(100) NOT NULL,
	nom_controlador_funcion_mod varchar(100) NOT NULL default '',
	modulo_padre_cve int4 NULL,
	is_seccion numeric(1) DEFAULT 0,
	CONSTRAINT sse_modulo_pkey PRIMARY KEY (modulo_cve),
	CONSTRAINT fk_padre_modulo FOREIGN KEY (modulo_padre_cve) REFERENCES encuestas.sse_modulo(modulo_cve)
)
WITH (
	OIDS=FALSE
);


CREATE TABLE encuestas.sse_modulo_rol (
	modulo_cve int4 NOT NULL,
	role_id int4 NOT NULL,
	acceso numeric(1) DEFAULT 0,
	CONSTRAINT sse_modulo_role_pkey PRIMARY KEY (modulo_cve, role_id),
	CONSTRAINT fk_modulo_cve_r_role FOREIGN KEY (modulo_cve) REFERENCES encuestas.sse_modulo(modulo_cve)
)
WITH (
	OIDS=FALSE
);

 --
 --Crear tablas para guardar designación de evaluación de autoevaluación
 -- Ejecución LEAS 03/02/2017
 CREATE TABLE encuestas.sse_designar_autoeveluaciones (
	des_autoevaluacion_cve serial,
	reglas_evaluacion_cve int4 NOT NULL,
	grupos_ids_text varchar(256) null,
	evaluado_user_cve int4 NOT NULL,
	evaluador_user_cve int4 NOT NULL,
	course_cve int4 NOT NULL,
	CONSTRAINT sse_designar_autoeveluaciones_pkey PRIMARY KEY (des_autoevaluacion_cve),
	CONSTRAINT fk_reglas_evaluacion_cve FOREIGN KEY (reglas_evaluacion_cve) REFERENCES encuestas.sse_reglas_evaluacion(reglas_evaluacion_cve)
)
WITH (
	OIDS=FALSE
);

ALTER TABLE encuestas.sse_evaluacion ALTER COLUMN reactivos_cve DROP NOT NULL; --Permite que la columna permita nulos Ejecución Luis petición Christian 

ALTER TABLE encuestas.sse_evaluacion ALTER COLUMN acceso numeric(1) DEFAULT 0;