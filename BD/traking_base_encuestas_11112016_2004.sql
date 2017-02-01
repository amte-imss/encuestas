-- Ejecución LEAS 11-11-2016 
ALTER TABLE encuestas.sse_respuestas ADD COLUMN texto_real varchar(1000) null; -- Se agrego el campo valor real, para agilizar los cálculos, es decir, "casi siempre" equivale a "si", "casi nunca" equivale a "No", "No envió mensaje" a "No aplica"

--- Modifica la entidad "sse_result_evaluacion" del esquema de encuestas -- Ejecución LEAS 15/11/2016
CREATE TABLE encuestas.sse_result_evaluacion_encuesta_curso (
	evaluacion_resul_cve int8 NOT NULL DEFAULT nextval('encuestas.sse_result_evaluacion_evaluacion_resul_cve_seq'::regclass),
	encuesta_cve int4 NOT NULL,
	course_cve int4 NOT NULL,
	group_id int4 NOT NULL,
	evaluado_user_cve int4 NOT NULL,
	evaluador_user_cve int4 NOT NULL,
	total_puntua_si int4 NOT NULL DEFAULT 0,
	total_nos int4 NOT NULL DEFAULT 0,
	total_no_puntua_napv int4 NOT NULL DEFAULT 0,
	total_reactivos_bono int4 NOT NULL DEFAULT 0,
	base int4 NOT NULL DEFAULT 0,
	calif_emitida numeric(6,3) NOT NULL DEFAULT 0,
	CONSTRAINT sse_result_evaluacion_encuesta_cursopkey PRIMARY KEY (evaluacion_resul_cve)
)
WITH (
	OIDS=FALSE
);

ALTER SEQUENCE encuestas.sse_indicador RESTART WITH 1; --reinicia contador de "encuestas.sse_indicador"

alter table encuestas.sse_preguntas add
  CONSTRAINT fkpre_indicador
  FOREIGN KEY (tipo_indicador_cve) 
  REFERENCES  encuestas.sse_indicador(indicador_cve);
  
 ---Agregrega fecha en la insersión de un registro Ejecución LEAS 17-11-2016
ALTER TABLE encuestas.sse_result_evaluacion_encuesta_curso ADD COLUMN fecha_add timestamp ;--agrega columna 
alter table encuestas.sse_result_evaluacion_encuesta_curso alter column fecha_add set default current_timestamp; --agrega current timestamp

alter table encuestas.sse_evaluacion alter column fecha set default current_timestamp; ç

-- Ejecución LEAS fecha 23/11/2016
---Agregar entidad para la administracin por bloque 
CREATE TABLE encuestas.sse_curso_bloque_grupo (
	course_cve int4 NOT NULL,
	mdl_groups_cve int4 NOT NULL,
	bloque int4 NOT NULL,
	CONSTRAINT sse_curso_bloque_grupopkey PRIMARY KEY (course_cve, mdl_groups_cve, bloque)
)
WITH (
	OIDS=FALSE
);

ALTER TABLE encuestas.sse_reglas_evaluacion ADD COLUMN eval_is_satisfaccion numeric (1);--saber si es encuesta de satisfacción o de desempeño

ALTER TABLE encuestas.sse_result_evaluacion_encuesta_curso DROP COLUMN eval_is_satisfaccion;--demas equivocación

--Ejecución LEAS fecha 07/12/2016 Agrega campos para poder saber el rol que valido y el rol a quién se valida ---------------------
------------------------------------------------------  No se llevo a cabo ----------------------------------------------------
ALTER TABLE encuestas.sse_result_evaluacion_encuesta_curso ADD COLUMN evaluador_rol_id int4 null;
ALTER TABLE encuestas.sse_result_evaluacion_encuesta_curso ADD COLUMN evaluado_rol_id int4 null;

ALTER TABLE encuestas.sse_result_evaluacion_encuesta_curso DROP COLUMN evaluado_rol_id int4;
ALTER TABLE encuestas.sse_result_evaluacion_encuesta_curso DROP COLUMN evaluador_rol_id int4;

-----------Ejecución agregar campos para identificar umae, unidad_normativa y clave
ALTER TABLE departments.ssd_cat_dependencia ADD COLUMN is_umae char(1) null;
ALTER TABLE departments.ssd_cat_dependencia ADD COLUMN cve_unidad char(10) null;
ALTER TABLE departments.ssd_cat_dependencia ADD column cve_normativa char(10) null;

CREATE TABLE departments.ssd_regiones (
	cve_regiones int4 NOT NULL,
	name_region varchar(20),
	CONSTRAINT ssd_region_pkey PRIMARY KEY (cve_regiones)
)
WITH (
	OIDS=FALSE
);

alter table departments.ssd_cat_delegacion add column cve_regiones int4;
alter table departments.ssd_cat_delegacion add
  CONSTRAINT fkcve_regiones
  FOREIGN KEY (cve_regiones) 
  REFERENCES  departments.ssd_regiones(cve_regiones);

update departments.ssd_cat_dependencia set is_umae = 0;
update departments.ssd_cat_dependencia set is_umae = 1 where cve_depto_adscripcion like '%0000' and ind_umae=1;

---Agreagar a departamentos región Ejecución Jesús Díaz  13/12/2016
-- Agregar "default current_timestamp" a tala encuestas.sse_evaluacion
alter table encuestas.sse_evaluacion alter column fecha set default current_timestamp;

--Agregar columna para guardar grupos que califican a CT 20/12/2016  ejecución Luis, pedido por Jesús, Hilda y Elizabeth
alter table encuestas.sse_evaluacion add column grupos_ids_text varchar(256);
alter table encuestas.sse_result_evaluacion_encuesta_curso add column grupos_ids_text varchar(256);

--
-- Actualiza relación de la delegación con sus regiones 
--
update departments.ssd_cat_delegacion set cve_regiones = 1 where cve_delegacion in ('01', '02', '03', '06', '11', '14', '17', '19', '26', '27');
update departments.ssd_cat_delegacion set cve_regiones = 2 where cve_delegacion in ('05', '08', '10', '20', '25', '29', '34');
update departments.ssd_cat_delegacion set cve_regiones = 3 where cve_delegacion in ('07', '37', '38', '12', '18', '21', '22', '23', '28', '30', '31', '32');
update departments.ssd_cat_delegacion set cve_regiones = 4 where cve_delegacion in ('13', '15', '16', '24', '33', '35', '36', '04');

--
-- Agregar campo de texto de descripción de la encuesta que aplica para las implementaciones
-- Ejecución LEAS 13/10/2017
--

alter table encuestas.sse_encuestas add column guia_descripcion_encuesta text null;


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

