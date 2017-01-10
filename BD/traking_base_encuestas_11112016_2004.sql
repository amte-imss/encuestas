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
