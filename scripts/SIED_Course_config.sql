ALTER TABLE public.mdl_course_config 
ADD tutorizado NUMERIC(1,0);

ALTER TABLE public.mdl_course_config  
ADD CONSTRAINT CK_cur_tutorizado CHECK(tutorizado IN (1,2)),


--ADD 
--	curso_alcance VARCHAR(20),

--ADD 
--	CONSTRAINT CK_curso_alcance CHECK(curso_alcance='Actualizacin' OR curso_alcance='CapacitaciÛn' OR curso_alcance='FormaciÛn' );