--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: encuestas; Type: SCHEMA; Schema: -; Owner: innovaedu
--

CREATE SCHEMA encuestas;


ALTER SCHEMA encuestas OWNER TO innovaedu;

SET search_path = encuestas, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: sse_encuesta_curso; Type: TABLE; Schema: encuestas; Owner: postgres; Tablespace: 
--

CREATE TABLE sse_encuesta_curso (
    encuesta_curso_cve integer NOT NULL,
    mdl_groups_cve integer,
    course_cve integer NOT NULL,
    encuesta_cve integer NOT NULL,
    alcance_curso_cve integer NOT NULL
);


ALTER TABLE sse_encuesta_curso OWNER TO postgres;

--
-- Name: sse_encuesta_curso_encuesta_curso_cve_seq; Type: SEQUENCE; Schema: encuestas; Owner: postgres
--

CREATE SEQUENCE sse_encuesta_curso_encuesta_curso_cve_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE sse_encuesta_curso_encuesta_curso_cve_seq OWNER TO postgres;

--
-- Name: sse_encuesta_curso_encuesta_curso_cve_seq; Type: SEQUENCE OWNED BY; Schema: encuestas; Owner: postgres
--

ALTER SEQUENCE sse_encuesta_curso_encuesta_curso_cve_seq OWNED BY sse_encuesta_curso.encuesta_curso_cve;


--
-- Name: sse_encuestas; Type: TABLE; Schema: encuestas; Owner: postgres; Tablespace: 
--

CREATE TABLE sse_encuestas (
    encuesta_cve integer NOT NULL,
    descripcion_encuestas character varying(300),
    is_bono numeric(1,0) DEFAULT 0 NOT NULL,
    rol_id bigint,
    tutorizado numeric(1,0) DEFAULT 0 NOT NULL
);


ALTER TABLE sse_encuestas OWNER TO postgres;

--
-- Name: sse_encuestas_encuesta_cve_seq; Type: SEQUENCE; Schema: encuestas; Owner: postgres
--

CREATE SEQUENCE sse_encuestas_encuesta_cve_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE sse_encuestas_encuesta_cve_seq OWNER TO postgres;

--
-- Name: sse_encuestas_encuesta_cve_seq; Type: SEQUENCE OWNED BY; Schema: encuestas; Owner: postgres
--

ALTER SEQUENCE sse_encuestas_encuesta_cve_seq OWNED BY sse_encuestas.encuesta_cve;


--
-- Name: sse_evaluacion; Type: TABLE; Schema: encuestas; Owner: guagnelli; Tablespace: 
--

CREATE TABLE sse_evaluacion (
    evaluacion_cve bigint NOT NULL,
    encuesta_cve integer NOT NULL,
    preguntas_cve integer NOT NULL,
    reactivos_cve integer NOT NULL,
    course_cve bigint NOT NULL,
    group_id bigint NOT NULL,
    evaluado_user_cve bigint NOT NULL,
    evaluado_rol_id bigint NOT NULL,
    evaluador_user_cve bigint NOT NULL,
    evaluador_rol_id bigint NOT NULL,
    respuesta_abierta text,
    fecha timestamp without time zone NOT NULL
);


ALTER TABLE sse_evaluacion OWNER TO guagnelli;

--
-- Name: sse_evaluacion_evaluacion_cve_seq; Type: SEQUENCE; Schema: encuestas; Owner: guagnelli
--

CREATE SEQUENCE sse_evaluacion_evaluacion_cve_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE sse_evaluacion_evaluacion_cve_seq OWNER TO guagnelli;

--
-- Name: sse_evaluacion_evaluacion_cve_seq; Type: SEQUENCE OWNED BY; Schema: encuestas; Owner: guagnelli
--

ALTER SEQUENCE sse_evaluacion_evaluacion_cve_seq OWNED BY sse_evaluacion.evaluacion_cve;


--
-- Name: sse_preguntas; Type: TABLE; Schema: encuestas; Owner: postgres; Tablespace: 
--

CREATE TABLE sse_preguntas (
    preguntas_cve integer NOT NULL,
    seccion_cve integer NOT NULL,
    encuesta_cve integer NOT NULL,
    tipo_pregunta_cve integer NOT NULL,
    pregunta character varying(500),
    pregunta_abierta_cerrada character varying(20)
);


ALTER TABLE sse_preguntas OWNER TO postgres;

--
-- Name: sse_preguntas_preguntas_cve_seq; Type: SEQUENCE; Schema: encuestas; Owner: postgres
--

CREATE SEQUENCE sse_preguntas_preguntas_cve_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE sse_preguntas_preguntas_cve_seq OWNER TO postgres;

--
-- Name: sse_preguntas_preguntas_cve_seq; Type: SEQUENCE OWNED BY; Schema: encuestas; Owner: postgres
--

ALTER SEQUENCE sse_preguntas_preguntas_cve_seq OWNED BY sse_preguntas.preguntas_cve;


--
-- Name: sse_reglas_evaluacion; Type: TABLE; Schema: encuestas; Owner: postgres; Tablespace: 
--

CREATE TABLE sse_reglas_evaluacion (
    reglas_evaluacion_cve integer NOT NULL,
    rol_evaluado_cve integer NOT NULL,
    rol_evaluador_cve integer NOT NULL,
    is_excepcion numeric(1,0) DEFAULT 0 NOT NULL,
    tutorizado numeric(1,0) DEFAULT 0 NOT NULL
);


ALTER TABLE sse_reglas_evaluacion OWNER TO postgres;

--
-- Name: sse_reglas_evaluacion_reglas_evaluacion_cve_seq; Type: SEQUENCE; Schema: encuestas; Owner: postgres
--

CREATE SEQUENCE sse_reglas_evaluacion_reglas_evaluacion_cve_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE sse_reglas_evaluacion_reglas_evaluacion_cve_seq OWNER TO postgres;

--
-- Name: sse_reglas_evaluacion_reglas_evaluacion_cve_seq; Type: SEQUENCE OWNED BY; Schema: encuestas; Owner: postgres
--

ALTER SEQUENCE sse_reglas_evaluacion_reglas_evaluacion_cve_seq OWNED BY sse_reglas_evaluacion.reglas_evaluacion_cve;


--
-- Name: sse_respuestas; Type: TABLE; Schema: encuestas; Owner: postgres; Tablespace: 
--

CREATE TABLE sse_respuestas (
    reactivos_cve integer NOT NULL,
    preguntas_cve integer NOT NULL,
    ponderacion integer,
    texto character varying(1000),
    orden numeric(2,0),
    encuesta_cve integer NOT NULL
);


ALTER TABLE sse_respuestas OWNER TO postgres;

--
-- Name: sse_respuestas_reactivos_cve_seq; Type: SEQUENCE; Schema: encuestas; Owner: postgres
--

CREATE SEQUENCE sse_respuestas_reactivos_cve_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE sse_respuestas_reactivos_cve_seq OWNER TO postgres;

--
-- Name: sse_respuestas_reactivos_cve_seq; Type: SEQUENCE OWNED BY; Schema: encuestas; Owner: postgres
--

ALTER SEQUENCE sse_respuestas_reactivos_cve_seq OWNED BY sse_respuestas.reactivos_cve;


--
-- Name: sse_seccion; Type: TABLE; Schema: encuestas; Owner: postgres; Tablespace: 
--

CREATE TABLE sse_seccion (
    seccion_cve integer NOT NULL,
    descripcion character varying(30)
);


ALTER TABLE sse_seccion OWNER TO postgres;

--
-- Name: sse_seccion_seccion_cve_seq; Type: SEQUENCE; Schema: encuestas; Owner: postgres
--

CREATE SEQUENCE sse_seccion_seccion_cve_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE sse_seccion_seccion_cve_seq OWNER TO postgres;

--
-- Name: sse_seccion_seccion_cve_seq; Type: SEQUENCE OWNED BY; Schema: encuestas; Owner: postgres
--

ALTER SEQUENCE sse_seccion_seccion_cve_seq OWNED BY sse_seccion.seccion_cve;


--
-- Name: sse_tipo_pregunta; Type: TABLE; Schema: encuestas; Owner: postgres; Tablespace: 
--

CREATE TABLE sse_tipo_pregunta (
    tipo_pregunta_cve integer NOT NULL,
    descripcion character varying(300)
);


ALTER TABLE sse_tipo_pregunta OWNER TO postgres;

--
-- Name: sse_tipo_pregunta_tipo_pregunta_cve_seq; Type: SEQUENCE; Schema: encuestas; Owner: postgres
--

CREATE SEQUENCE sse_tipo_pregunta_tipo_pregunta_cve_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE sse_tipo_pregunta_tipo_pregunta_cve_seq OWNER TO postgres;

--
-- Name: sse_tipo_pregunta_tipo_pregunta_cve_seq; Type: SEQUENCE OWNED BY; Schema: encuestas; Owner: postgres
--

ALTER SEQUENCE sse_tipo_pregunta_tipo_pregunta_cve_seq OWNED BY sse_tipo_pregunta.tipo_pregunta_cve;


--
-- Name: encuesta_curso_cve; Type: DEFAULT; Schema: encuestas; Owner: postgres
--

ALTER TABLE ONLY sse_encuesta_curso ALTER COLUMN encuesta_curso_cve SET DEFAULT nextval('sse_encuesta_curso_encuesta_curso_cve_seq'::regclass);


--
-- Name: encuesta_cve; Type: DEFAULT; Schema: encuestas; Owner: postgres
--

ALTER TABLE ONLY sse_encuestas ALTER COLUMN encuesta_cve SET DEFAULT nextval('sse_encuestas_encuesta_cve_seq'::regclass);


--
-- Name: evaluacion_cve; Type: DEFAULT; Schema: encuestas; Owner: guagnelli
--

ALTER TABLE ONLY sse_evaluacion ALTER COLUMN evaluacion_cve SET DEFAULT nextval('sse_evaluacion_evaluacion_cve_seq'::regclass);


--
-- Name: preguntas_cve; Type: DEFAULT; Schema: encuestas; Owner: postgres
--

ALTER TABLE ONLY sse_preguntas ALTER COLUMN preguntas_cve SET DEFAULT nextval('sse_preguntas_preguntas_cve_seq'::regclass);


--
-- Name: reglas_evaluacion_cve; Type: DEFAULT; Schema: encuestas; Owner: postgres
--

ALTER TABLE ONLY sse_reglas_evaluacion ALTER COLUMN reglas_evaluacion_cve SET DEFAULT nextval('sse_reglas_evaluacion_reglas_evaluacion_cve_seq'::regclass);


--
-- Name: reactivos_cve; Type: DEFAULT; Schema: encuestas; Owner: postgres
--

ALTER TABLE ONLY sse_respuestas ALTER COLUMN reactivos_cve SET DEFAULT nextval('sse_respuestas_reactivos_cve_seq'::regclass);


--
-- Name: seccion_cve; Type: DEFAULT; Schema: encuestas; Owner: postgres
--

ALTER TABLE ONLY sse_seccion ALTER COLUMN seccion_cve SET DEFAULT nextval('sse_seccion_seccion_cve_seq'::regclass);


--
-- Name: tipo_pregunta_cve; Type: DEFAULT; Schema: encuestas; Owner: postgres
--

ALTER TABLE ONLY sse_tipo_pregunta ALTER COLUMN tipo_pregunta_cve SET DEFAULT nextval('sse_tipo_pregunta_tipo_pregunta_cve_seq'::regclass);


--
-- Data for Name: sse_encuesta_curso; Type: TABLE DATA; Schema: encuestas; Owner: postgres
--

COPY sse_encuesta_curso (encuesta_curso_cve, mdl_groups_cve, course_cve, encuesta_cve, alcance_curso_cve) FROM stdin;
3	12367	823	1	2
\.


--
-- Name: sse_encuesta_curso_encuesta_curso_cve_seq; Type: SEQUENCE SET; Schema: encuestas; Owner: postgres
--

SELECT pg_catalog.setval('sse_encuesta_curso_encuesta_curso_cve_seq', 3, true);


--
-- Data for Name: sse_encuestas; Type: TABLE DATA; Schema: encuestas; Owner: postgres
--

COPY sse_encuestas (encuesta_cve, descripcion_encuestas, is_bono, rol_id, tutorizado) FROM stdin;
1	Evaluación del desempeño del coordinador de curso tutorizado a distancia(coordinador de implementación evalúa coordinador de curso tutorizado)\n	0	\N	0
\.


--
-- Name: sse_encuestas_encuesta_cve_seq; Type: SEQUENCE SET; Schema: encuestas; Owner: postgres
--

SELECT pg_catalog.setval('sse_encuestas_encuesta_cve_seq', 1, true);


--
-- Data for Name: sse_evaluacion; Type: TABLE DATA; Schema: encuestas; Owner: guagnelli
--

COPY sse_evaluacion (evaluacion_cve, encuesta_cve, preguntas_cve, reactivos_cve, course_cve, group_id, evaluado_user_cve, evaluado_rol_id, evaluador_user_cve, evaluador_rol_id, respuesta_abierta, fecha) FROM stdin;
\.


--
-- Name: sse_evaluacion_evaluacion_cve_seq; Type: SEQUENCE SET; Schema: encuestas; Owner: guagnelli
--

SELECT pg_catalog.setval('sse_evaluacion_evaluacion_cve_seq', 1, false);


--
-- Data for Name: sse_preguntas; Type: TABLE DATA; Schema: encuestas; Owner: postgres
--

COPY sse_preguntas (preguntas_cve, seccion_cve, encuesta_cve, tipo_pregunta_cve, pregunta, pregunta_abierta_cerrada) FROM stdin;
\.


--
-- Name: sse_preguntas_preguntas_cve_seq; Type: SEQUENCE SET; Schema: encuestas; Owner: postgres
--

SELECT pg_catalog.setval('sse_preguntas_preguntas_cve_seq', 7, true);


--
-- Data for Name: sse_reglas_evaluacion; Type: TABLE DATA; Schema: encuestas; Owner: postgres
--

COPY sse_reglas_evaluacion (reglas_evaluacion_cve, rol_evaluado_cve, rol_evaluador_cve, is_excepcion, tutorizado) FROM stdin;
\.


--
-- Name: sse_reglas_evaluacion_reglas_evaluacion_cve_seq; Type: SEQUENCE SET; Schema: encuestas; Owner: postgres
--

SELECT pg_catalog.setval('sse_reglas_evaluacion_reglas_evaluacion_cve_seq', 1, false);


--
-- Data for Name: sse_respuestas; Type: TABLE DATA; Schema: encuestas; Owner: postgres
--

COPY sse_respuestas (reactivos_cve, preguntas_cve, ponderacion, texto, orden, encuesta_cve) FROM stdin;
\.


--
-- Name: sse_respuestas_reactivos_cve_seq; Type: SEQUENCE SET; Schema: encuestas; Owner: postgres
--

SELECT pg_catalog.setval('sse_respuestas_reactivos_cve_seq', 20, true);


--
-- Data for Name: sse_seccion; Type: TABLE DATA; Schema: encuestas; Owner: postgres
--

COPY sse_seccion (seccion_cve, descripcion) FROM stdin;
1	presentación del curso
\.


--
-- Name: sse_seccion_seccion_cve_seq; Type: SEQUENCE SET; Schema: encuestas; Owner: postgres
--

SELECT pg_catalog.setval('sse_seccion_seccion_cve_seq', 1, true);


--
-- Data for Name: sse_tipo_pregunta; Type: TABLE DATA; Schema: encuestas; Owner: postgres
--

COPY sse_tipo_pregunta (tipo_pregunta_cve, descripcion) FROM stdin;
1	opción multiple
2	texto libre
3	si/no
4	nula - opcion multiple
5	nula - si/no
6	nula - texto libre
\.


--
-- Name: sse_tipo_pregunta_tipo_pregunta_cve_seq; Type: SEQUENCE SET; Schema: encuestas; Owner: postgres
--

SELECT pg_catalog.setval('sse_tipo_pregunta_tipo_pregunta_cve_seq', 6, true);


--
-- Name: pk_evaluacion; Type: CONSTRAINT; Schema: encuestas; Owner: guagnelli; Tablespace: 
--

ALTER TABLE ONLY sse_evaluacion
    ADD CONSTRAINT pk_evaluacion PRIMARY KEY (evaluacion_cve);


--
-- Name: pk_preguntas; Type: CONSTRAINT; Schema: encuestas; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY sse_preguntas
    ADD CONSTRAINT pk_preguntas PRIMARY KEY (preguntas_cve, encuesta_cve);


--
-- Name: pk_respuestas; Type: CONSTRAINT; Schema: encuestas; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY sse_respuestas
    ADD CONSTRAINT pk_respuestas PRIMARY KEY (encuesta_cve, preguntas_cve, reactivos_cve);


--
-- Name: sse_encuesta_curso_pkey; Type: CONSTRAINT; Schema: encuestas; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY sse_encuesta_curso
    ADD CONSTRAINT sse_encuesta_curso_pkey PRIMARY KEY (encuesta_curso_cve);


--
-- Name: sse_encuestas_pkey; Type: CONSTRAINT; Schema: encuestas; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY sse_encuestas
    ADD CONSTRAINT sse_encuestas_pkey PRIMARY KEY (encuesta_cve);


--
-- Name: sse_reglas_evaluacion_pkey; Type: CONSTRAINT; Schema: encuestas; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY sse_reglas_evaluacion
    ADD CONSTRAINT sse_reglas_evaluacion_pkey PRIMARY KEY (reglas_evaluacion_cve);


--
-- Name: sse_seccion_pkey; Type: CONSTRAINT; Schema: encuestas; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY sse_seccion
    ADD CONSTRAINT sse_seccion_pkey PRIMARY KEY (seccion_cve);


--
-- Name: sse_tipo_pregunta_pkey; Type: CONSTRAINT; Schema: encuestas; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY sse_tipo_pregunta
    ADD CONSTRAINT sse_tipo_pregunta_pkey PRIMARY KEY (tipo_pregunta_cve);


--
-- Name: xif1sse_preguntas; Type: INDEX; Schema: encuestas; Owner: postgres; Tablespace: 
--

CREATE INDEX xif1sse_preguntas ON sse_preguntas USING btree (tipo_pregunta_cve);


--
-- Name: xif1sse_reglas_evaluacion; Type: INDEX; Schema: encuestas; Owner: postgres; Tablespace: 
--

CREATE INDEX xif1sse_reglas_evaluacion ON sse_reglas_evaluacion USING btree (rol_evaluador_cve);


--
-- Name: xif2sse_encuesta_curso; Type: INDEX; Schema: encuestas; Owner: postgres; Tablespace: 
--

CREATE INDEX xif2sse_encuesta_curso ON sse_encuesta_curso USING btree (alcance_curso_cve);


--
-- Name: xif2sse_preguntas; Type: INDEX; Schema: encuestas; Owner: postgres; Tablespace: 
--

CREATE INDEX xif2sse_preguntas ON sse_preguntas USING btree (encuesta_cve);


--
-- Name: xif2sse_reglas_evaluacion; Type: INDEX; Schema: encuestas; Owner: postgres; Tablespace: 
--

CREATE INDEX xif2sse_reglas_evaluacion ON sse_reglas_evaluacion USING btree (rol_evaluado_cve);


--
-- Name: xif2sse_respuestas; Type: INDEX; Schema: encuestas; Owner: postgres; Tablespace: 
--

CREATE INDEX xif2sse_respuestas ON sse_respuestas USING btree (preguntas_cve);


--
-- Name: xif3sse_encuesta_curso; Type: INDEX; Schema: encuestas; Owner: postgres; Tablespace: 
--

CREATE INDEX xif3sse_encuesta_curso ON sse_encuesta_curso USING btree (encuesta_cve);


--
-- Name: xif3sse_preguntas; Type: INDEX; Schema: encuestas; Owner: postgres; Tablespace: 
--

CREATE INDEX xif3sse_preguntas ON sse_preguntas USING btree (seccion_cve);


--
-- Name: xif4sse_encuesta_curso; Type: INDEX; Schema: encuestas; Owner: postgres; Tablespace: 
--

CREATE INDEX xif4sse_encuesta_curso ON sse_encuesta_curso USING btree (course_cve);


--
-- Name: xif6sse_encuesta_curso; Type: INDEX; Schema: encuestas; Owner: postgres; Tablespace: 
--

CREATE INDEX xif6sse_encuesta_curso ON sse_encuesta_curso USING btree (mdl_groups_cve);


--
-- Name: fk_respuesta_evaluacion; Type: FK CONSTRAINT; Schema: encuestas; Owner: guagnelli
--

ALTER TABLE ONLY sse_evaluacion
    ADD CONSTRAINT fk_respuesta_evaluacion FOREIGN KEY (encuesta_cve, preguntas_cve, reactivos_cve) REFERENCES sse_respuestas(encuesta_cve, preguntas_cve, reactivos_cve);


--
-- Name: fk_respuesta_pregunta; Type: FK CONSTRAINT; Schema: encuestas; Owner: postgres
--

ALTER TABLE ONLY sse_respuestas
    ADD CONSTRAINT fk_respuesta_pregunta FOREIGN KEY (encuesta_cve, preguntas_cve) REFERENCES sse_preguntas(encuesta_cve, preguntas_cve);


--
-- Name: r_10; Type: FK CONSTRAINT; Schema: encuestas; Owner: postgres
--

ALTER TABLE ONLY sse_encuesta_curso
    ADD CONSTRAINT r_10 FOREIGN KEY (encuesta_cve) REFERENCES sse_encuestas(encuesta_cve);


--
-- Name: r_17; Type: FK CONSTRAINT; Schema: encuestas; Owner: postgres
--

ALTER TABLE ONLY sse_preguntas
    ADD CONSTRAINT r_17 FOREIGN KEY (tipo_pregunta_cve) REFERENCES sse_tipo_pregunta(tipo_pregunta_cve);


--
-- Name: r_30; Type: FK CONSTRAINT; Schema: encuestas; Owner: postgres
--

ALTER TABLE ONLY sse_preguntas
    ADD CONSTRAINT r_30 FOREIGN KEY (encuesta_cve) REFERENCES sse_encuestas(encuesta_cve);


--
-- Name: r_31; Type: FK CONSTRAINT; Schema: encuestas; Owner: postgres
--

ALTER TABLE ONLY sse_preguntas
    ADD CONSTRAINT r_31 FOREIGN KEY (seccion_cve) REFERENCES sse_seccion(seccion_cve);


--
-- PostgreSQL database dump complete
--

