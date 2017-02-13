ALTER TABLE encuestas.sse_encuestas 
ADD status NUMERIC(1,0) DEFAULT 1 NOT NULL;

ALTER TABLE encuestas.sse_encuestas  
ADD CONSTRAINT CK_inst_status CHECK(status IN (0,1));



