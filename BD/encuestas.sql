--
-- PostgreSQL database dump
--

-- Dumped from database version 9.5.1
-- Dumped by pg_dump version 9.5.1

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: encuestas; Type: SCHEMA; Schema: -; Owner: innovaedu
--

CREATE SCHEMA encuestas;


ALTER SCHEMA encuestas OWNER TO innovaedu;

SET search_path = encuestas, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: sse_alcance_curso; Type: TABLE; Schema: encuestas; Owner: postgres
--

CREATE TABLE sse_alcance_curso (
    alcance_curso_cve integer NOT NULL,
    descripcion character varying(30)
);


ALTER TABLE sse_alcance_curso OWNER TO postgres;

--
-- Name: sse_alcance_curso_alcance_curso_cve_seq; Type: SEQUENCE; Schema: encuestas; Owner: postgres
--

CREATE SEQUENCE sse_alcance_curso_alcance_curso_cve_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE sse_alcance_curso_alcance_curso_cve_seq OWNER TO postgres;

--
-- Name: sse_alcance_curso_alcance_curso_cve_seq; Type: SEQUENCE OWNED BY; Schema: encuestas; Owner: postgres
--

ALTER SEQUENCE sse_alcance_curso_alcance_curso_cve_seq OWNED BY sse_alcance_curso.alcance_curso_cve;


--
-- Name: sse_condicion_accion; Type: TABLE; Schema: encuestas; Owner: postgres
--

CREATE TABLE sse_condicion_accion (
    see_condicion_accion integer NOT NULL,
    condicion_pregunta_cve integer NOT NULL,
    preguntas_cve integer NOT NULL
);


ALTER TABLE sse_condicion_accion OWNER TO postgres;

--
-- Name: sse_condicion_accion_see_condicion_accion_seq; Type: SEQUENCE; Schema: encuestas; Owner: postgres
--

CREATE SEQUENCE sse_condicion_accion_see_condicion_accion_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE sse_condicion_accion_see_condicion_accion_seq OWNER TO postgres;

--
-- Name: sse_condicion_accion_see_condicion_accion_seq; Type: SEQUENCE OWNED BY; Schema: encuestas; Owner: postgres
--

ALTER SEQUENCE sse_condicion_accion_see_condicion_accion_seq OWNED BY sse_condicion_accion.see_condicion_accion;


--
-- Name: sse_condicionales_pregunta; Type: TABLE; Schema: encuestas; Owner: postgres
--

CREATE TABLE sse_condicionales_pregunta (
    condicion_pregunta_cve integer NOT NULL,
    andor character varying(10),
    reactivos_cve integer NOT NULL,
    operador character varying(20)
);


ALTER TABLE sse_condicionales_pregunta OWNER TO postgres;

--
-- Name: sse_condicionales_pregunta_condicion_pregunta_cve_seq; Type: SEQUENCE; Schema: encuestas; Owner: postgres
--

CREATE SEQUENCE sse_condicionales_pregunta_condicion_pregunta_cve_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE sse_condicionales_pregunta_condicion_pregunta_cve_seq OWNER TO postgres;

--
-- Name: sse_condicionales_pregunta_condicion_pregunta_cve_seq; Type: SEQUENCE OWNED BY; Schema: encuestas; Owner: postgres
--

ALTER SEQUENCE sse_condicionales_pregunta_condicion_pregunta_cve_seq OWNED BY sse_condicionales_pregunta.condicion_pregunta_cve;


--
-- Name: sse_encuesta_curso; Type: TABLE; Schema: encuestas; Owner: postgres
--

CREATE TABLE sse_encuesta_curso (
    encuesta_curso_cve integer NOT NULL,
    mdl_groups_cve integer,
    evaluado_user_cve integer NOT NULL,
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
-- Name: sse_encuestas; Type: TABLE; Schema: encuestas; Owner: postgres
--

CREATE TABLE sse_encuestas (
    encuesta_cve integer NOT NULL,
    descripcion_encuestas character varying(300)
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
-- Name: sse_evaluacion; Type: TABLE; Schema: encuestas; Owner: postgres
--

CREATE TABLE sse_evaluacion (
    evaluacion_cve integer NOT NULL,
    preguntas_cve integer NOT NULL,
    encuesta_curso_cve integer NOT NULL,
    evaluador_user_cve integer NOT NULL,
    reactivos_cve integer NOT NULL,
    respuesta_abierta character varying(1000)
);


ALTER TABLE sse_evaluacion OWNER TO postgres;

--
-- Name: sse_evaluacion_evaluacion_cve_seq; Type: SEQUENCE; Schema: encuestas; Owner: postgres
--

CREATE SEQUENCE sse_evaluacion_evaluacion_cve_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE sse_evaluacion_evaluacion_cve_seq OWNER TO postgres;

--
-- Name: sse_evaluacion_evaluacion_cve_seq; Type: SEQUENCE OWNED BY; Schema: encuestas; Owner: postgres
--

ALTER SEQUENCE sse_evaluacion_evaluacion_cve_seq OWNED BY sse_evaluacion.evaluacion_cve;


--
-- Name: sse_preguntas; Type: TABLE; Schema: encuestas; Owner: postgres
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
-- Name: sse_reglas_evaluacion; Type: TABLE; Schema: encuestas; Owner: postgres
--

CREATE TABLE sse_reglas_evaluacion (
    reglas_evaluacion_cve integer NOT NULL,
    rol_evaluado_cve integer NOT NULL,
    rol_evaluador_cve integer NOT NULL
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
-- Name: sse_respuestas; Type: TABLE; Schema: encuestas; Owner: postgres
--

CREATE TABLE sse_respuestas (
    reactivos_cve integer NOT NULL,
    preguntas_cve integer NOT NULL,
    ponderacion integer,
    texto character varying(1000)
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
-- Name: sse_seccion; Type: TABLE; Schema: encuestas; Owner: postgres
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
-- Name: sse_tipo_curso; Type: TABLE; Schema: encuestas; Owner: postgres
--

CREATE TABLE sse_tipo_curso (
    tipo_curso_cve integer NOT NULL,
    curse_cve integer NOT NULL,
    descripcion character varying(100)
);


ALTER TABLE sse_tipo_curso OWNER TO postgres;

--
-- Name: sse_tipo_curso_tipo_curso_cve_seq; Type: SEQUENCE; Schema: encuestas; Owner: postgres
--

CREATE SEQUENCE sse_tipo_curso_tipo_curso_cve_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE sse_tipo_curso_tipo_curso_cve_seq OWNER TO postgres;

--
-- Name: sse_tipo_curso_tipo_curso_cve_seq; Type: SEQUENCE OWNED BY; Schema: encuestas; Owner: postgres
--

ALTER SEQUENCE sse_tipo_curso_tipo_curso_cve_seq OWNED BY sse_tipo_curso.tipo_curso_cve;


--
-- Name: sse_tipo_pregunta; Type: TABLE; Schema: encuestas; Owner: postgres
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
-- Name: alcance_curso_cve; Type: DEFAULT; Schema: encuestas; Owner: postgres
--

ALTER TABLE ONLY sse_alcance_curso ALTER COLUMN alcance_curso_cve SET DEFAULT nextval('sse_alcance_curso_alcance_curso_cve_seq'::regclass);


--
-- Name: see_condicion_accion; Type: DEFAULT; Schema: encuestas; Owner: postgres
--

ALTER TABLE ONLY sse_condicion_accion ALTER COLUMN see_condicion_accion SET DEFAULT nextval('sse_condicion_accion_see_condicion_accion_seq'::regclass);


--
-- Name: condicion_pregunta_cve; Type: DEFAULT; Schema: encuestas; Owner: postgres
--

ALTER TABLE ONLY sse_condicionales_pregunta ALTER COLUMN condicion_pregunta_cve SET DEFAULT nextval('sse_condicionales_pregunta_condicion_pregunta_cve_seq'::regclass);


--
-- Name: encuesta_curso_cve; Type: DEFAULT; Schema: encuestas; Owner: postgres
--

ALTER TABLE ONLY sse_encuesta_curso ALTER COLUMN encuesta_curso_cve SET DEFAULT nextval('sse_encuesta_curso_encuesta_curso_cve_seq'::regclass);


--
-- Name: encuesta_cve; Type: DEFAULT; Schema: encuestas; Owner: postgres
--

ALTER TABLE ONLY sse_encuestas ALTER COLUMN encuesta_cve SET DEFAULT nextval('sse_encuestas_encuesta_cve_seq'::regclass);


--
-- Name: evaluacion_cve; Type: DEFAULT; Schema: encuestas; Owner: postgres
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
-- Name: tipo_curso_cve; Type: DEFAULT; Schema: encuestas; Owner: postgres
--

ALTER TABLE ONLY sse_tipo_curso ALTER COLUMN tipo_curso_cve SET DEFAULT nextval('sse_tipo_curso_tipo_curso_cve_seq'::regclass);


--
-- Name: tipo_pregunta_cve; Type: DEFAULT; Schema: encuestas; Owner: postgres
--

ALTER TABLE ONLY sse_tipo_pregunta ALTER COLUMN tipo_pregunta_cve SET DEFAULT nextval('sse_tipo_pregunta_tipo_pregunta_cve_seq'::regclass);


--
-- Data for Name: sse_alcance_curso; Type: TABLE DATA; Schema: encuestas; Owner: postgres
--

COPY sse_alcance_curso (alcance_curso_cve, descripcion) FROM stdin;
1	CURSO
2	DIPLOMADO
\.


--
-- Name: sse_alcance_curso_alcance_curso_cve_seq; Type: SEQUENCE SET; Schema: encuestas; Owner: postgres
--

SELECT pg_catalog.setval('sse_alcance_curso_alcance_curso_cve_seq', 2, true);


--
-- Data for Name: sse_condicion_accion; Type: TABLE DATA; Schema: encuestas; Owner: postgres
--

COPY sse_condicion_accion (see_condicion_accion, condicion_pregunta_cve, preguntas_cve) FROM stdin;
\.


--
-- Name: sse_condicion_accion_see_condicion_accion_seq; Type: SEQUENCE SET; Schema: encuestas; Owner: postgres
--

SELECT pg_catalog.setval('sse_condicion_accion_see_condicion_accion_seq', 1, false);


--
-- Data for Name: sse_condicionales_pregunta; Type: TABLE DATA; Schema: encuestas; Owner: postgres
--

COPY sse_condicionales_pregunta (condicion_pregunta_cve, andor, reactivos_cve, operador) FROM stdin;
\.


--
-- Name: sse_condicionales_pregunta_condicion_pregunta_cve_seq; Type: SEQUENCE SET; Schema: encuestas; Owner: postgres
--

SELECT pg_catalog.setval('sse_condicionales_pregunta_condicion_pregunta_cve_seq', 1, false);


--
-- Data for Name: sse_encuesta_curso; Type: TABLE DATA; Schema: encuestas; Owner: postgres
--

COPY sse_encuesta_curso (encuesta_curso_cve, mdl_groups_cve, evaluado_user_cve, course_cve, encuesta_cve, alcance_curso_cve) FROM stdin;
3	12367	15436	823	1	2
\.


--
-- Name: sse_encuesta_curso_encuesta_curso_cve_seq; Type: SEQUENCE SET; Schema: encuestas; Owner: postgres
--

SELECT pg_catalog.setval('sse_encuesta_curso_encuesta_curso_cve_seq', 3, true);


--
-- Data for Name: sse_encuestas; Type: TABLE DATA; Schema: encuestas; Owner: postgres
--

COPY sse_encuestas (encuesta_cve, descripcion_encuestas) FROM stdin;
1	Evaluación del desempeño del coordinador de curso tutorizado a distancia(coordinador de implementación evalúa coordinador de curso tutorizado)\n
\.


--
-- Name: sse_encuestas_encuesta_cve_seq; Type: SEQUENCE SET; Schema: encuestas; Owner: postgres
--

SELECT pg_catalog.setval('sse_encuestas_encuesta_cve_seq', 1, true);


--
-- Data for Name: sse_evaluacion; Type: TABLE DATA; Schema: encuestas; Owner: postgres
--

COPY sse_evaluacion (evaluacion_cve, preguntas_cve, encuesta_curso_cve, evaluador_user_cve, reactivos_cve, respuesta_abierta) FROM stdin;
7	5	3	35664	10	\N
8	6	3	35664	15	\N
9	7	3	35664	20	\N
10	5	3	35665	10	\N
11	6	3	35665	13	\N
13	5	3	35666	10	\N
14	6	3	35666	13	\N
15	7	3	35666	20	\N
16	5	3	35667	9	\N
17	6	3	35667	13	\N
18	7	3	35667	20	\N
22	5	3	35676	10	\N
23	6	3	35676	14	\N
24	7	3	35676	19	\N
25	5	3	35677	10	\N
26	6	3	35677	15	\N
27	7	3	35677	20	\N
28	5	3	35678	9	\N
29	6	3	35678	15	\N
30	7	3	35678	20	\N
31	5	3	35679	10	\N
32	6	3	35679	14	\N
33	7	3	35679	20	\N
12	7	3	35665	19	\N
\.


--
-- Name: sse_evaluacion_evaluacion_cve_seq; Type: SEQUENCE SET; Schema: encuestas; Owner: postgres
--

SELECT pg_catalog.setval('sse_evaluacion_evaluacion_cve_seq', 33, true);


--
-- Data for Name: sse_preguntas; Type: TABLE DATA; Schema: encuestas; Owner: postgres
--

COPY sse_preguntas (preguntas_cve, seccion_cve, encuesta_cve, tipo_pregunta_cve, pregunta, pregunta_abierta_cerrada) FROM stdin;
5	1	1	1	Verifico el ingreso de los coordinadores de tutores a la plataforma 5 días hábiles antes de la apertura del curso	\N
6	1	1	1	En el caso de algún coordinador de tutores no ingreso a  la plataforma en la temporalidad establecida al inicio de un curso, se comunicó vía correo electrónico o vía telefónica a fin de identificar si tiene algún problema de acceso al curso	\N
7	1	1	1	Solicito por correo electrónico o vía telefónica a los coordinadores de tutores que presentaron problemas de acceso	\N
\.


--
-- Name: sse_preguntas_preguntas_cve_seq; Type: SEQUENCE SET; Schema: encuestas; Owner: postgres
--

SELECT pg_catalog.setval('sse_preguntas_preguntas_cve_seq', 7, true);


--
-- Data for Name: sse_reglas_evaluacion; Type: TABLE DATA; Schema: encuestas; Owner: postgres
--

COPY sse_reglas_evaluacion (reglas_evaluacion_cve, rol_evaluado_cve, rol_evaluador_cve) FROM stdin;
\.


--
-- Name: sse_reglas_evaluacion_reglas_evaluacion_cve_seq; Type: SEQUENCE SET; Schema: encuestas; Owner: postgres
--

SELECT pg_catalog.setval('sse_reglas_evaluacion_reglas_evaluacion_cve_seq', 1, false);


--
-- Data for Name: sse_respuestas; Type: TABLE DATA; Schema: encuestas; Owner: postgres
--

COPY sse_respuestas (reactivos_cve, preguntas_cve, ponderacion, texto) FROM stdin;
6	5	0	Nunca
7	5	0	Casi nunca
8	5	0	Algunas veces
9	5	1	Casi siempre
10	5	1	Siempre
11	6	0	Nunca
12	6	0	Casi nunca
13	6	0	Algunas veces
14	6	1	Casi siempre
15	6	1	Siempre
16	7	0	Nunca
17	7	0	Casi nunca
18	7	0	Algunas veces
19	7	1	Casi siempre
20	7	1	Siempre
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
-- Data for Name: sse_tipo_curso; Type: TABLE DATA; Schema: encuestas; Owner: postgres
--

COPY sse_tipo_curso (tipo_curso_cve, curse_cve, descripcion) FROM stdin;
1	823	TUTORIZADO
\.


--
-- Name: sse_tipo_curso_tipo_curso_cve_seq; Type: SEQUENCE SET; Schema: encuestas; Owner: postgres
--

SELECT pg_catalog.setval('sse_tipo_curso_tipo_curso_cve_seq', 1, true);


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
-- Name: pk_sse_alcance_curso; Type: CONSTRAINT; Schema: encuestas; Owner: postgres
--

ALTER TABLE ONLY sse_alcance_curso
    ADD CONSTRAINT pk_sse_alcance_curso PRIMARY KEY (alcance_curso_cve);


--
-- Name: sse_condicion_accion_pkey; Type: CONSTRAINT; Schema: encuestas; Owner: postgres
--

ALTER TABLE ONLY sse_condicion_accion
    ADD CONSTRAINT sse_condicion_accion_pkey PRIMARY KEY (see_condicion_accion);


--
-- Name: sse_condicionales_pregunta_pkey; Type: CONSTRAINT; Schema: encuestas; Owner: postgres
--

ALTER TABLE ONLY sse_condicionales_pregunta
    ADD CONSTRAINT sse_condicionales_pregunta_pkey PRIMARY KEY (condicion_pregunta_cve);


--
-- Name: sse_encuesta_curso_pkey; Type: CONSTRAINT; Schema: encuestas; Owner: postgres
--

ALTER TABLE ONLY sse_encuesta_curso
    ADD CONSTRAINT sse_encuesta_curso_pkey PRIMARY KEY (encuesta_curso_cve);


--
-- Name: sse_encuestas_pkey; Type: CONSTRAINT; Schema: encuestas; Owner: postgres
--

ALTER TABLE ONLY sse_encuestas
    ADD CONSTRAINT sse_encuestas_pkey PRIMARY KEY (encuesta_cve);


--
-- Name: sse_evaluacion_pkey; Type: CONSTRAINT; Schema: encuestas; Owner: postgres
--

ALTER TABLE ONLY sse_evaluacion
    ADD CONSTRAINT sse_evaluacion_pkey PRIMARY KEY (evaluacion_cve);


--
-- Name: sse_preguntas_pkey; Type: CONSTRAINT; Schema: encuestas; Owner: postgres
--

ALTER TABLE ONLY sse_preguntas
    ADD CONSTRAINT sse_preguntas_pkey PRIMARY KEY (preguntas_cve);


--
-- Name: sse_reglas_evaluacion_pkey; Type: CONSTRAINT; Schema: encuestas; Owner: postgres
--

ALTER TABLE ONLY sse_reglas_evaluacion
    ADD CONSTRAINT sse_reglas_evaluacion_pkey PRIMARY KEY (reglas_evaluacion_cve);


--
-- Name: sse_respuestas_pkey; Type: CONSTRAINT; Schema: encuestas; Owner: postgres
--

ALTER TABLE ONLY sse_respuestas
    ADD CONSTRAINT sse_respuestas_pkey PRIMARY KEY (reactivos_cve);


--
-- Name: sse_seccion_pkey; Type: CONSTRAINT; Schema: encuestas; Owner: postgres
--

ALTER TABLE ONLY sse_seccion
    ADD CONSTRAINT sse_seccion_pkey PRIMARY KEY (seccion_cve);


--
-- Name: sse_tipo_curso_pkey; Type: CONSTRAINT; Schema: encuestas; Owner: postgres
--

ALTER TABLE ONLY sse_tipo_curso
    ADD CONSTRAINT sse_tipo_curso_pkey PRIMARY KEY (tipo_curso_cve);


--
-- Name: sse_tipo_pregunta_pkey; Type: CONSTRAINT; Schema: encuestas; Owner: postgres
--

ALTER TABLE ONLY sse_tipo_pregunta
    ADD CONSTRAINT sse_tipo_pregunta_pkey PRIMARY KEY (tipo_pregunta_cve);


--
-- Name: xif1sse_condicion_accion; Type: INDEX; Schema: encuestas; Owner: postgres
--

CREATE INDEX xif1sse_condicion_accion ON sse_condicion_accion USING btree (condicion_pregunta_cve);


--
-- Name: xif1sse_condicionales_pregunta; Type: INDEX; Schema: encuestas; Owner: postgres
--

CREATE INDEX xif1sse_condicionales_pregunta ON sse_condicionales_pregunta USING btree (reactivos_cve);


--
-- Name: xif1sse_evaluacion; Type: INDEX; Schema: encuestas; Owner: postgres
--

CREATE INDEX xif1sse_evaluacion ON sse_evaluacion USING btree (reactivos_cve);


--
-- Name: xif1sse_preguntas; Type: INDEX; Schema: encuestas; Owner: postgres
--

CREATE INDEX xif1sse_preguntas ON sse_preguntas USING btree (tipo_pregunta_cve);


--
-- Name: xif1sse_reglas_evaluacion; Type: INDEX; Schema: encuestas; Owner: postgres
--

CREATE INDEX xif1sse_reglas_evaluacion ON sse_reglas_evaluacion USING btree (rol_evaluador_cve);


--
-- Name: xif1sse_tipo_curso; Type: INDEX; Schema: encuestas; Owner: postgres
--

CREATE INDEX xif1sse_tipo_curso ON sse_tipo_curso USING btree (curse_cve);


--
-- Name: xif2sse_condicion_accion; Type: INDEX; Schema: encuestas; Owner: postgres
--

CREATE INDEX xif2sse_condicion_accion ON sse_condicion_accion USING btree (preguntas_cve);


--
-- Name: xif2sse_encuesta_curso; Type: INDEX; Schema: encuestas; Owner: postgres
--

CREATE INDEX xif2sse_encuesta_curso ON sse_encuesta_curso USING btree (alcance_curso_cve);


--
-- Name: xif2sse_evaluacion; Type: INDEX; Schema: encuestas; Owner: postgres
--

CREATE INDEX xif2sse_evaluacion ON sse_evaluacion USING btree (evaluador_user_cve);


--
-- Name: xif2sse_preguntas; Type: INDEX; Schema: encuestas; Owner: postgres
--

CREATE INDEX xif2sse_preguntas ON sse_preguntas USING btree (encuesta_cve);


--
-- Name: xif2sse_reglas_evaluacion; Type: INDEX; Schema: encuestas; Owner: postgres
--

CREATE INDEX xif2sse_reglas_evaluacion ON sse_reglas_evaluacion USING btree (rol_evaluado_cve);


--
-- Name: xif2sse_respuestas; Type: INDEX; Schema: encuestas; Owner: postgres
--

CREATE INDEX xif2sse_respuestas ON sse_respuestas USING btree (preguntas_cve);


--
-- Name: xif3sse_encuesta_curso; Type: INDEX; Schema: encuestas; Owner: postgres
--

CREATE INDEX xif3sse_encuesta_curso ON sse_encuesta_curso USING btree (encuesta_cve);


--
-- Name: xif3sse_evaluacion; Type: INDEX; Schema: encuestas; Owner: postgres
--

CREATE INDEX xif3sse_evaluacion ON sse_evaluacion USING btree (encuesta_curso_cve);


--
-- Name: xif3sse_preguntas; Type: INDEX; Schema: encuestas; Owner: postgres
--

CREATE INDEX xif3sse_preguntas ON sse_preguntas USING btree (seccion_cve);


--
-- Name: xif4sse_encuesta_curso; Type: INDEX; Schema: encuestas; Owner: postgres
--

CREATE INDEX xif4sse_encuesta_curso ON sse_encuesta_curso USING btree (course_cve);


--
-- Name: xif4sse_evaluacion; Type: INDEX; Schema: encuestas; Owner: postgres
--

CREATE INDEX xif4sse_evaluacion ON sse_evaluacion USING btree (preguntas_cve);


--
-- Name: xif5sse_encuesta_curso; Type: INDEX; Schema: encuestas; Owner: postgres
--

CREATE INDEX xif5sse_encuesta_curso ON sse_encuesta_curso USING btree (evaluado_user_cve);


--
-- Name: xif6sse_encuesta_curso; Type: INDEX; Schema: encuestas; Owner: postgres
--

CREATE INDEX xif6sse_encuesta_curso ON sse_encuesta_curso USING btree (mdl_groups_cve);


--
-- Name: r_10; Type: FK CONSTRAINT; Schema: encuestas; Owner: postgres
--

ALTER TABLE ONLY sse_encuesta_curso
    ADD CONSTRAINT r_10 FOREIGN KEY (encuesta_cve) REFERENCES sse_encuestas(encuesta_cve);


--
-- Name: r_13; Type: FK CONSTRAINT; Schema: encuestas; Owner: postgres
--

ALTER TABLE ONLY sse_condicionales_pregunta
    ADD CONSTRAINT r_13 FOREIGN KEY (reactivos_cve) REFERENCES sse_respuestas(reactivos_cve);


--
-- Name: r_14; Type: FK CONSTRAINT; Schema: encuestas; Owner: postgres
--

ALTER TABLE ONLY sse_condicion_accion
    ADD CONSTRAINT r_14 FOREIGN KEY (condicion_pregunta_cve) REFERENCES sse_condicionales_pregunta(condicion_pregunta_cve);


--
-- Name: r_17; Type: FK CONSTRAINT; Schema: encuestas; Owner: postgres
--

ALTER TABLE ONLY sse_preguntas
    ADD CONSTRAINT r_17 FOREIGN KEY (tipo_pregunta_cve) REFERENCES sse_tipo_pregunta(tipo_pregunta_cve);


--
-- Name: r_18; Type: FK CONSTRAINT; Schema: encuestas; Owner: postgres
--

ALTER TABLE ONLY sse_evaluacion
    ADD CONSTRAINT r_18 FOREIGN KEY (reactivos_cve) REFERENCES sse_respuestas(reactivos_cve);


--
-- Name: r_20; Type: FK CONSTRAINT; Schema: encuestas; Owner: postgres
--

ALTER TABLE ONLY sse_evaluacion
    ADD CONSTRAINT r_20 FOREIGN KEY (encuesta_curso_cve) REFERENCES sse_encuesta_curso(encuesta_curso_cve);


--
-- Name: r_3; Type: FK CONSTRAINT; Schema: encuestas; Owner: postgres
--

ALTER TABLE ONLY sse_respuestas
    ADD CONSTRAINT r_3 FOREIGN KEY (preguntas_cve) REFERENCES sse_preguntas(preguntas_cve);


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
-- Name: r_38; Type: FK CONSTRAINT; Schema: encuestas; Owner: postgres
--

ALTER TABLE ONLY sse_evaluacion
    ADD CONSTRAINT r_38 FOREIGN KEY (preguntas_cve) REFERENCES sse_preguntas(preguntas_cve);


--
-- Name: r_41; Type: FK CONSTRAINT; Schema: encuestas; Owner: postgres
--

ALTER TABLE ONLY sse_condicion_accion
    ADD CONSTRAINT r_41 FOREIGN KEY (preguntas_cve) REFERENCES sse_preguntas(preguntas_cve);


--
-- PostgreSQL database dump complete
--

