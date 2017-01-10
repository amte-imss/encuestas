ALTER TABLE encuestas.sse_preguntas 
ADD obligada NUMERIC(1,0) DEFault 0 NOT NULL,
ADD orden    INT,
ADD is_bono NUMERIC(1,0),
ADD has_children numeric(1,0) default 0 not null,
ADD obligatoria numeric(1,0),
add val_ref inte,
add pregunta_padre int,
ADD CONSTRAINT fk_padre_preguntas
FOREIGN KEY (pregunta_padre)
REFERENCES encuestas.sse_preguntas(preguntas_cve);


