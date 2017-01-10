--drop innecesary tables
DROP TABLE encuestas.sse_tipo_curso;
DROP TABLE encuestas.sse_alcance_curso;
DROP TABLE encuestas.sse_condicion_accion;
DROP TABLE encuestas.sse_condicionales_pregunta;

--Add columns to "encuestas"
Alter table encuestas.sse_encuestas
ADD COLUMN tutorizado numeric(1) DEFAULT 0 NOT NULL,
ADD COLUMN is_bono numeric(1) DEFAULT 0 NOT NULL ,
ADD COLUMN rol_id int8 NULL;


--CHANGE PK to preguntas
ALTER TABLE encuestas.sse_preguntas
DROP CONSTRAINT sse_preguntas_pkey CASCADE;

ALTER TABLE encuestas.sse_preguntas
ADD CONSTRAINT pk_preguntas
PRIMARY KEY(preguntas_cve, encuesta_cve);


--Change relationship between "preguntas" and "Respuestas"
TRUNCATE encuestas.sse_respuestas;

ALTER TABLE encuestas.sse_respuestas
ADD COLUMN encuesta_cve INT NOT NULL,

ADD CONSTRAINT fk_respuesta_pregunta
FOREIGN KEY (encuesta_cve, preguntas_cve)
REFERENCES encuestas.sse_preguntas(encuesta_cve, preguntas_cve),

DROP CONSTRAINT sse_respuestas_pkey,

ADD CONSTRAINT pk_respuestas
PRIMARY KEY(encuesta_cve, preguntas_cve, reactivos_cve);

--
ALTER TABLE encuestas.sse_encuesta_curso 
DROP COLUMN evaluado_user_cve;

ALTER TABLE encuestas.sse_reglas_evaluacion
ADD COLUMN is_excepcion numeric(1) DEFAULT 0 NOT NULL,
ADD COLUMN tutorizado numeric(1) DEFAULT 0 NOT NULL;
    
ALTER TABLE encuestas.sse_respuestas
ADD COLUMN orden numeric(2);

drop table encuestas.sse_evaluacion;
CREATE TABLE encuestas.sse_evaluacion (
	evaluacion_cve bigserial,
	encuesta_cve INT NOT NULL,
    preguntas_cve INT NOT NULL,
    reactivos_cve int NOT NULL,
    course_cve bigint not null,
    group_id bigint not null,
    evaluado_user_cve bigint NOT NULL,
    evaluado_rol_id bigint NOT NULL,
	evaluador_user_cve bigint NOT NULL,
    evaluador_rol_id bigint NOT NULL,
	respuesta_abierta TEXT NULL,
    fecha timestamp NOT NULL,
	CONSTRAINT pk_evaluacion 
    PRIMARY KEY (evaluacion_cve),
    CONSTRAINT fk_respuesta_evaluacion
    FOREIGN KEY (encuesta_cve, preguntas_cve, reactivos_cve)
    REFERENCES encuestas.sse_respuestas(encuesta_cve, preguntas_cve, reactivos_cve)
);


ALTER TABLE encuestas.sse_preguntas 
ADD obligada NUMERIC(1,0) DEFault 0 NOT NULL,
ADD orden    INT,
ADD is_bono NUMERIC(1,0),
ADD has_children numeric(1,0) default 0 not null,
ADD obligatoria numeric(1,0),
add val_ref int,
add pregunta_padre int,
ADD encuesta_padre int,
ADD CONSTRAINT fk_padre_preguntas
FOREIGN KEY (pregunta_padre,encuesta_padre)
REFERENCES encuestas.sse_preguntas(preguntas_cve,encuesta_cve);
