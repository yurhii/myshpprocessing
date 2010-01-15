drop table cesis;
create table[cesis] ([cesi] varchar(255) not null , [estado] varchar(255) not null , [municipio] varchar(255) not null , [localidad] varchar(255) not null , [cv_edo] varchar(255) not null , [cv_mun] varchar(255) not null , [cv_loc] varchar(255) not null , [clave] varchar(50) not null  primary key , [calle] varchar(255) not null , [numero] int not null , [detalle] varchar(255) not null , [colonia] varchar(255) not null , [cp] int not null , [lada] int not null , [telefono_1] varchar(255) not null , [telefono_2] varchar(255) not null , [telefono_3] varchar(255) not null , [lat] float not null , [lon] float not null , [xCoor] int not null , [yCoor] int not null  );
CREATE INDEX cesi ON [cesis] (cesi);
		CREATE INDEX estado ON [cesis] (estado);
		CREATE INDEX municipio ON [cesis] (municipio);
		CREATE INDEX localidad ON [cesis] (localidad);
		CREATE INDEX cv_edo ON [cesis] (cv_edo);
		CREATE INDEX cv_mun ON [cesis] (cv_mun);
		CREATE INDEX cv_loc ON [cesis] (cv_loc);
		CREATE INDEX calle ON [cesis] (calle);
		CREATE INDEX numero ON [cesis] (numero);
		CREATE INDEX detalle ON [cesis] (detalle);
		CREATE INDEX colonia ON [cesis] (colonia);
		CREATE INDEX cp ON [cesis] (cp);
		CREATE INDEX lada ON [cesis] (lada);
		CREATE INDEX telefono_1 ON [cesis] (telefono_1);
		CREATE INDEX telefono_2 ON [cesis] (telefono_2);
		CREATE INDEX telefono_3 ON [cesis] (telefono_3);
		CREATE INDEX lat ON [cesis] (lat);
		CREATE INDEX lon ON [cesis] (lon);
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('AGUASCALIENTES', 'AGUASCALIENTES', 'AGUASCALIENTES', 'AGUASCALIENTES', '01', '001', '0001', '010010001', 'SIERRA PINTADA', '102', 'PLANTA BAJA', 'FRACCIONAMIENTO BOSQUES DEL PRADO', '20127', '449', '912-2405', '', '', '21.91206', '-102.31169', '-10231169', '-2191206');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('ENSENADA', 'BAJA CALIFORNIA', 'ENSENADA', 'ENSENADA', '02', '001', '0001', '020010001', 'AV BLANCARTE', '415', 'LOC. 1, 2, 3', 'ZONA CENTRO', '22800', '646', '174-0633', '', '', '31.86462', '-116.62021', '-11662021', '-3186462');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('MEXICALI', 'BAJA CALIFORNIA', 'MEXICALI', 'MEXICALI', '02', '002', '0001', '020020001', 'PASEO DE LOS HEROES', '298', '', 'CENTRO', '21000', '686', '558-5000', '', '', '32.6398', '-115.503', '-11550300', '-3263980');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('TIJUANA', 'BAJA CALIFORNIA', 'TIJUANA', 'TIJUANA', '02', '004', '0001', '020040001', 'AV GUANAJUATO', '102', '', 'CACHO', '22400', '664', '688-3593', '', '', '32.51854', '-117.02528', '-11702528', '-3251854');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('LA PAZ', 'BAJA CALIFORNIA SUR', 'LA PAZ', 'LA PAZ', '03', '003', '0001', '030030001', 'IGNACIO ALTAMIRANO', '722', 'ESQ. NICOLAS BRAVO', 'GUERRERO', '23000', '612', '125-3655', '', '', '24.15467', '-110.31296', '-11031296', '-2415467');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('LOS CABOS', 'BAJA CALIFORNIA SUR', 'LOS CABOS', 'SAN JOSE DEL CABO', '03', '008', '0001', '030080001', 'BARLOVENTO Y SOTAVENTO', '0', '', 'ROSARITO', '23400', '624', '120-5151', '', '', '23.06830707', '-109.70632979', '-10970633', '-2306831');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('CAMPECHE', 'CAMPECHE', 'CAMPECHE', 'CAMPECHE', '04', '002', '0001', '040020001', 'LORENZO ALFARO Y ALOMIA', '0', 'LOT. 43 MANZ. K, POR AV. FUNDADORES', 'AREA AH KIM PECH', '24010', '981', '127-1704', '', '', '19.85329', '-90.52573', '-9052573', '-1985329');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('CIUDAD DEL CARMEN', 'CAMPECHE', 'CARMEN', 'CIUDAD DEL CARMEN', '04', '003', '0001', '040030001', 'CALLE 31', '173', '', 'AVIACION ', '24170', '938', '131-0882', '131-0884', '', '18.64308', '-91.82326', '-9182326', '-1864308');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('TAPACHULA', 'CHIAPAS', 'TAPACHULA', 'TAPACHULA', '07', '089', '0001', '070890001', 'CALLE 15 PONIENTE', '0', 'ENTRE 4A AV NTE Y 2A AV NTE.', 'CENTRO', '30700', '962', '118-1480', '118-1485', '', '14.91368', '-92.26029', '-9226029', '-1491368');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('TUXTLA GUTIERREZ', 'CHIAPAS', 'TUXTLA GUTIERREZ', 'TUXTLA GUTIERREZ', '07', '101', '0001', '071010001', 'BLVD BELISARIO DOMINGUEZ', '2452', '', 'FRACCIONAMIENTO RESIDENCIAL CAMPESTRE', '29010', '961', '121-4777', '', '', '16.75465', '-93.13672', '-9313672', '-1675465');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('CIUDAD JUAREZ', 'CHIHUAHUA', 'JUAREZ', 'JUAREZ', '08', '037', '0001', '080370001', 'AV TEOFILO BORUNDA', '7387', '', 'ACEQUIAS', '32612', '656', '558-4340', '', '', '31.68921', '-106.41874', '-10641874', '-3168921');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('CHIHUAHUA', 'CHIHUAHUA', 'CHIHUAHUA', 'CHIHUAHUA', '08', '019', '0001', '080190001', 'ALLENDE', '1500', '', 'CENTRO', '31000', '614', '412-9040', '', '', '28.63906', '-106.07024', '-10607024', '-2863906');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('MONCLOVA', 'COAHUILA', 'MONCLOVA', 'MONCLOVA', '05', '018', '0001', '050180001', 'BLVD HAROLD R PAPE', '803', 'LOC. 3-4, ENTRE CALLE ESTANDAR Y AV. 3', 'OBRERA SUR', '25750', '866', '636-5790', '', '', '26.87256', '-101.4273', '-10142730', '-2687256');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('PIEDRAS NEGRAS', 'COAHUILA', 'PIEDRAS NEGRAS', 'PIEDRAS NEGRAS', '05', '025', '0001', '050250001', 'CALLE ANAHUAC', '701', 'ENTRE ABASOLO Y LOPEZ MATEOS', 'CENTRO', '26000', '878', '782-5454', '', '', '28.70529', '-100.52197', '-10052197', '-2870529');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('SALTILLO', 'COAHUILA', 'SALTILLO', 'SALTILLO', '05', '030', '0001', '050300001', 'BLVD NAZARIO ORTIZ GARZA', '2598', '', 'RESIDENCIAL LOS LAGOS', '25253', '844', '485-1350', '', '', '25.45735', '-100.9774', '-10097740', '-2545735');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('TORREON', 'COAHUILA', 'TORREON', 'TORREON', '05', '035', '0001', '050350001', 'CALZ COLON', '280', 'ESQ. MARIANO ESCOBEDO', 'CENTRO', '27000', '871', '711-0380', '', '', '25.54415', '-103.44838', '-10344838', '-2554415');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('COLIMA', 'COLIMA', 'COLIMA', 'COLIMA', '06', '002', '0001', '060020001', 'VENUSTIANO CARRANZA', '1360', '', 'FRACCIONAMIENTO SANTA BARBARA', '28017', '312', '313-6866', '', '', '19.26871', '-103.7167', '-10371670', '-1926871');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('MANZANILLO', 'COLIMA', 'MANZANILLO', 'MANZANILLO', '06', '007', '0001', '060070001', 'AV MANZANILLO', '152', 'ENTRE ROSA MORADA Y ELIAS ZAMORA VERDUZCO', 'ARBOLEDAS', '28869', '314', '333-2250', '', '', '19.12168', '-104.3396', '-10433960', '-1912168');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('CUAUTITLAN', 'MEXICO', 'CUAUTITLAN IZCALLI', 'CUAUTITLAN IZCALLI', '15', '121', '0001', '151210001', 'AUTOPISTA MEXICO-QUERETARO', '0', 'LOC E-01, ESQ. AV. CHALMA', 'JARDINES DE LA HACIENDA SUR', '54720', '55', '2472-0360', '2472-0361', '', '19.67002', '-99.20082', '-9920082', '-1967002');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('TLALNEPANTLA', 'MEXICO', 'TLALNEPANTLA DE BAZ', 'TLALNEPANTLA', '15', '104', '0001', '151040001', 'AV PRESIDENTE JUAREZ', '2034', '', 'INDUSTRIAL PUENTE DE VIGAS', '54070', '55', '5366-4300', '5366-4311', '', '19.51388', '-99.21304', '-9921304', '-1951388');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('TOLUCA', 'MEXICO', 'TOLUCA', 'TOLUCA DE LERDO', '15', '106', '0001', '151060001', 'AV INDEPENDENCIA OTE', '620', 'ESQ. JOSEFA ORTIZ DOMINGUEZ', 'SANTA CLARA', '50060', '722', '167-2650', '', '', '19.29214', '-99.64731', '-9964731', '-1929214');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('BARRANCA DEL MUERTO', 'DISTRITO FEDERAL', 'ALVARO OBREGON', 'ALVARO OBREGON', '09', '010', '0001', '090100001', 'BARRANCA DEL MUERTO', '280', 'PISO 1', 'GUADALUPE INN', '1029', '55', '5322-6300', '', '', '19.36159', '-99.18596', '-9918596', '-1936159');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('CTM', 'DISTRITO FEDERAL', 'CUAUHTEMOC', 'CUAUHTEMOC', '09', '015', '0001', '090150001', 'VALLARTA', '0', '', 'TABACALERA', '6030', '55', '5566-0189', '', '', '19.43518', '-99.15558', '-9915558', '-1943518');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('COAPA', 'DISTRITO FEDERAL', 'TLALPAN', 'TLALPAN', '09', '012', '0001', '090120001', 'AV PROL DIVISION DEL NORTE', '4541', '', 'EJIDOS DE HUIPULCO', '14380', '55', '2652-0324', '2652-0325', '', '19.28933', '-99.12968', '-9912968', '-1928933');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('ERMITA', 'DISTRITO FEDERAL', 'IZTAPALAPA', 'IZTAPALAPA', '09', '007', '0001', '090070001', 'CALZ ERMITA IZTAPALAPA', '927', 'LOCAL  23, PLAZA ARISTEUM', 'SANTA ISABEL INDUSTRIAL', '9820', '55', '5581-8697', '5581-6049', '5581-6184', '19.35559', '-99.10136', '-9910136', '-1935559');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('LA VIGA', 'DISTRITO FEDERAL', 'VENUSTIANO CARRANZA', 'VENUSTIANO CARRANZA', '09', '017', '0001', '090170001', 'VIADUCTO RIO DE LA PIEDAD', '398', 'ESQ. CALZ. DE LA VIGA', 'JAMAICA', '15800', '55', '5741-5309', '5741-5315', '', '19.40461', '-99.1256', '-9912560', '-1940461');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('VALLEJO', 'DISTRITO FEDERAL', 'AZCAPOTZALCO', 'AZCAPOTZALCO', '09', '002', '0001', '090020001', 'AV NORTE 45', '909', 'LETRA E, ESQ. PONIENTE 134', 'INDUSTRIAL VALLEJO', '2300', '55', '5719-2819', '5719-2821', '', '19.49357', '-99.15966', '-9915966', '-1949357');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('DURANGO', 'DURANGO', 'DURANGO', 'VICTORIA DE DURANGO', '10', '005', '0001', '100050001', 'CALLE VOLANTIN', '404', 'ESQ. GRANADA', 'BARRIO DE ANALCO', '34138', '618', '825-8411', '', '', '24.01989', '-104.67601', '-10467601', '-2401989');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('GOMEZ PALACIO', 'DURANGO', 'GOMEZ PALACIO', 'GOMEZ PALACIO', '10', '007', '0001', '100070001', 'BLVD MIGUEL ALEMAN', '445', 'PLANTA BAJA', 'CAMPESTRE', '35080', '871', '715-8811', '', '', '25.55343', '-103.49473', '-10349473', '-2555343');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('CELAYA', 'GUANAJUATO', 'CELAYA', 'CELAYA', '11', '007', '0001', '110070001', 'BLVD LOPEZ MATEOS', '1403', '', 'RENACIMIENTO', '38040', '461', '614-5390', '', '', '20.51921', '-100.83326', '-10083326', '-2051921');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('IRAPUATO', 'GUANAJUATO', 'IRAPUATO', 'IRAPUATO', '11', '017', '0001', '110170001', 'BLVD VICENTE GUERRERO', '2001', 'INT. 19', 'FRACCIONAMIENTO GAMEZ', '36650', '462', '625-2326', '', '', '20.6894', '-101.35839', '-10135839', '-2068940');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('LEON', 'GUANAJUATO', 'LEON', 'LEON', '11', '020', '0001', '110200001', 'BLVD TORRES LANDA', '1004', '', 'SANTA CLARA', '37470', '477', '788-8300', '', '', '21.1006', '-101.67269', '-10167269', '-2110060');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('SALAMANCA', 'GUANAJUATO', 'SALAMANCA', 'SALAMANCA', '11', '027', '0001', '110270001', 'PORTAL DE LOS BRAVO', '115', 'LOC. 104 Y 105', 'CENTRO', '36700', '464', '648-3452', '', '', '20.569', '-101.19916', '-10119916', '-2056900');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('ACAPULCO', 'GUERRERO', 'ACAPULCO DE JUAREZ', 'ACAPULCO DE JUAREZ', '12', '001', '0001', '120010001', 'AV COSTERA MIGUEL ALEMAN', '123', '', 'FRACCIONAMIENTO MAGALLANES', '39670', '744', '485-6090', '', '', '16.85587', '-99.86105', '-9986105', '-1685587');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('PACHUCA', 'HIDALGO', 'PACHUCA DE SOTO', 'PACHUCA DE SOTO', '13', '048', '0001', '130480001', 'BLVD EVERARDO MARQUEZ', '101', 'PLANTA BAJA', 'LOMAS RESIDENCIAL', '42060', '771', '714-7800', '', '', '20.11042', '-98.74732', '-9874732', '-2011042');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('GUADALAJARA', 'JALISCO', 'GUADALAJARA', 'GUADALAJARA', '14', '039', '0001', '140390001', 'AV JUAN PALOMAR Y ARIAS', '37', '', 'VALLARTA SAN JORGE', '44690', '333', '880-1400', '', '', '20.67521', '-103.39365', '-10339365', '-2067521');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('LAZARO CARDENAS', 'MICHOACAN', 'LAZARO CARDENAS', 'CIUDAD LAZARO CARDENAS', '16', '052', '0001', '160520001', 'AV MELCHOR OCAMPO', '31', 'LOC. 3 Y 4', 'INFONAVIT NUEVO HORIZONTE', '60950', '753', '532-0720', '532-4963', '', '17.96137', '-102.2072', '-10220720', '-1796137');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('MORELIA', 'MICHOACAN', 'MORELIA', 'MORELIA', '16', '053', '0001', '160530001', 'SIERVO DE LA NACION', '2000', 'ESQ. LIBRAMIENTO PTE', 'FRACCIONAMIENTO LIBERTAD', '58170', '443', '326-1105', '', '', '19.68506', '-101.23673', '-10123673', '-1968506');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('URUAPAN', 'MICHOACAN', 'URUAPAN', 'URUAPAN', '16', '102', '0001', '161020001', 'PASEO LAZARO CARDENAS', '10', '', 'MORELOS', '60050', '452', '524-4007', '529-0601', '', '19.41771', '-102.05092', '-10205092', '-1941771');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('ZAMORA', 'MICHOACAN', 'ZAMORA', 'ZAMORA DE HIDALGO', '16', '108', '0001', '161080001', 'AV JUAREZ PONIENTE', '22', '', 'CENTRO', '59600', '351', '517-6180', '517-2495', '', '19.98941', '-102.28673', '-10228673', '-1998941');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('CUERNAVACA', 'MORELOS', 'CUERNAVACA', 'CUERNAVACA', '17', '007', '0001', '170070001', 'PROL AV CUAHUTEMOC', '120', '', 'CHAPULTEPEC', '62450', '777', '315-1150', '', '', '18.92081', '-99.21655', '-9921655', '-1892081');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('PUERTO VALLARTA', 'JALISCO', 'PUERTO VALLARTA', 'PUERTO VALLARTA', '14', '067', '0001', '140670001', 'AV LOS TULES', '178', 'LOC 14, 15, 16,  17', 'FRACCIONAMIENTO LOS TULES', '48310', '322', '224-2411', '', '', '20.641', '-105.233', '-10523300', '-2064100');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('TEPIC', 'NAYARIT', 'TEPIC', 'TEPIC', '18', '017', '0001', '180170001', 'AV INSURGENTES PTE Y AV LAS BRISAS', '0', 'PLAZA FIESTA TEPIC, LOC 2', 'FRACCIONAMIENTO LAS BRISAS', '63117', '311', '218-3585', '', '', '21.52061', '-104.92167', '-10492167', '-2152061');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('MONTERREY', 'NUEVO LEON', 'MONTERREY', 'MONTERREY', '19', '039', '0001', '190390001', 'ZARAGOZA SUR', '800', '', 'ZONA CENTRO', '64000', '818', '130-3100', '', '', '25.66841', '-100.31026', '-10031026', '-2566841');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('SAN NICOLAS DE LOS GARZA', 'NUEVO LEON', 'SAN NICOLAS DE LOS GARZA', 'SAN NICOLAS DE LOS GARZA', '19', '046', '0001', '190460001', 'MANUEL L BARRAGAN', '100', 'PLAZA UKALI', 'CASA BELLA', '66428', '81', '1231-0211', '', '', '25.74823', '-100.31058', '-10031058', '-2574823');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('TUXTEPEC', 'OAXACA', 'SAN JUAN BAUTISTA TUXTEPEC', 'SAN JUAN BAUTISTA TUXTEPEC', '20', '184', '0001', '201840001', 'BLVD AVILA CAMACHO', '132', '', 'LAZARO CARDENAS', '68340', '287', '875-8840', '', '', '18.08703', '-96.12575', '-9612575', '-1808703');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('JUCHITAN', 'OAXACA', 'JUCHITAN DE ZARAGOZA', 'JUCHITAN DE ZARAGOZA', '20', '043', '0001', '200430001', 'AV ALDAMA', '0', '', 'PRIMERA SECCI0N', '70000', '971', '712-3050', '', '', '16.43604', '-95.02037', '-9502037', '-1643604');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('OAXACA', 'OAXACA', 'OAXACA DE JUAREZ', 'OAXACA DE JUAREZ', '20', '067', '0001', '200670001', 'MARTIRES DE TACUBAYA', '400', '', 'SANTA MARIA IXCOTEL', '68100', '951', '132-8950', '32-8962', '', '17.06379', '-96.71757', '-9671757', '-1706379');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('PUEBLA', 'PUEBLA', 'PUEBLA', 'HEROICA PUEBLA DE ZARAGOZA', '21', '114', '0001', '211140001', 'AV 25 ORIENTE', '1012', '', 'BELLAVISTA', '72500', '222', '240-7280', '', '', '19.03114', '-98.19612', '-9819612', '-1903114');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('TEHUACAN', 'PUEBLA', 'TEHUACAN', 'TEHUACAN', '21', '156', '0001', '211560001', 'CALZ ADOLFO L. MATEOS', '3210', '', 'PLAZA TEOTIHUACAN', '75760', '238', '382-8618', '', '', '18.46644', '-97.41476', '-9741476', '-1846644');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('TEZIHUTLAN', 'PUEBLA', 'TEZIUTLAN', 'TEZIUTLAN', '21', '174', '0001', '211740001', 'HIDALGO', '1629', '', 'CENTRO', '73800', '231', '313-3249', '', '', '19.80849', '-97.36333', '-9736333', '-1980849');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('QUERETARO', 'QUERETARO', 'QUERETARO', 'QUERETARO', '22', '014', '0001', '220140001', 'AV EJERCITO REPUBLICANO', '119', 'PISO 2', 'CARRETA', '76050', '442', '223-4465', '', '', '20.59056', '-100.37663', '-10037663', '-2059056');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('SAN JUAN DEL RIO', 'QUERETARO', 'SAN JUAN DEL RIO', 'SAN JUAN DEL RIO', '22', '016', '0001', '220160001', 'EMILIANO ZAPATA', '0', 'LOC 35 Y 36', 'CENTRO', '76800', '427', '274-7958', '', '', '20.38699', '-100.00582', '-10000582', '-2038699');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('CANCUN', 'QUINTANA ROO', 'BENITO JUAREZ', 'CANCUN', '23', '005', '0001', '230050001', 'AV BONAMPAK', '0', 'PLAZA VIVENDI AMERICAS, ESQUINA NICHUPTE, LOCAL 1, ANCLA 1  ', 'SUPERMANZANA 8', '77504', '998', '802-1190', '', '', '21.14399', '-86.82179', '-8682179', '-2114399');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('CHETUMAL', 'QUINTANA ROO', 'OTHON P. BLANCO', 'CHETUMAL', '23', '004', '0001', '230040001', 'CALZ VERACRUZ', '63', 'ENTRE ALVARIO OBREGON E IGNACIO ZARAGOZA', 'BARRIO BRAVO', '77089', '988', '129-2654', '129-2655', '129-2656', '18.49708', '-88.29098', '-8829098', '-1849708');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('CIUDAD VALLES', 'SAN LUIS POTOSI', 'CIUDAD VALLES', 'CIUDAD VALLES', '24', '013', '0001', '240130001', 'BLVD MEXICO LAREDO', '0', 'ENTRE COMONFORT Y 16 DE SEPT', 'CENTRO', '79000', '481', '382-2238', '', '', '21.98929', '-99.01214', '-9901214', '-2198929');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('SAN LUIS POTOSI', 'SAN LUIS POTOSI', 'SAN LUIS POTOSI', 'SAN LUIS POTOSI', '24', '028', '0001', '240280001', 'VENUSTIANO CARRANZA', '720', '', 'MODERNA', '78250', '444', '818-0133', '', '', '22.15112', '-100.98282', '-10098282', '-2215112');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('LOS MOCHIS', 'SINALOA', 'AHOME', 'LOS MOCHIS', '25', '001', '0001', '250010001', 'AV HERIBERTO VALADEZ', '185', 'ESQ. GUILLERMO PRIETO', 'CENTRO', '81200', '668', '818-6155', '815-6030', '', '25.79613', '-108.9872', '-10898720', '-2579613');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('MAZATLAN', 'SINALOA', 'MAZATLAN', 'MAZATLAN', '25', '012', '0001', '250120001', 'FLAMINGO', '510', 'ESQ. AV. EJERCITO MEXICANO Y RIO PANUCO', 'FERROCARRILERA', '82013', '669', '982-1401', '982-1487', '982-1501', '23.21739', '-106.41718', '-10641718', '-2321739');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('CULIACAN', 'SINALOA', 'CULIACAN', 'CULIACAN ROSALES', '25', '006', '0001', '250060001', 'BLVD LOLA BELTRAN', '3427', '', 'PRADERA DORADA', '80058', '667', '146-9025', '', '', '24.81497', '-107.43075', '-10743075', '-2481497');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('CIUDAD OBREGON', 'SONORA', 'CAJEME', 'CIUDAD OBREGON', '26', '018', '0001', '260180001', 'AV NAINARI', '275', 'ESQ. CALLE 5 DE FEBRERO', 'CENTRO', '85000', '644', '415-6639', '', '', '27.49989', '-109.93826', '-10993826', '-2749989');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('GUAYMAS', 'SONORA', 'GUAYMAS', 'HEROICA GUAYMAS', '26', '029', '0001', '260290001', 'AV SERDAN', '75', 'Y CALLE 22, EDIF. LUVER LOC. 1 PLANTA BAJA', 'CENTRO', '85400', '622', '221-9060', '', '', '27.92361', '-110.8897', '-11088970', '-2792361');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('HERMOSILLO', 'SONORA', 'HERMOSILLO', 'HERMOSILLO', '26', '030', '0001', '260300001', 'PASEO DEL CANAL', '0', 'Y COMONFORT, EDIF. MEXICO PISO 1', 'CENTRO  DE GOBIERNO', '83270', '662', '213-5250', '', '', '29.0687', '-110.958', '-11095800', '-2906870');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('NOGALES', 'SONORA', 'NOGALES', 'HEROICA NOGALES', '26', '043', '0001', '260430001', 'ITSMO', '16', 'ENTRE PROL. A. OBREGON Y RET. DE CALLE ITSMO, PLAZA KINO', 'INDUSTRIAL', '0', '631', '319-0555', '', '', '31.27905', '-110.9386', '-11093860', '-3127905');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('SAN LUIS RIO COLORADO', 'SONORA', 'SAN LUIS RIO COLORADO', 'SAN LUIS RIO COLORADO', '26', '055', '0001', '260550001', 'AV MIGUEL HIDALGO', '273', 'ENTRE LAS CALLES 2° Y 3°', 'CENTRO', '83449', '653', '534-0637', '', '', '32.47962', '-114.78141', '-11478141', '-3247962');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('VILLAHERMOSA', 'TABASCO', 'CENTRO', 'VILLAHERMOSA', '27', '004', '0001', '270040001', 'PASEO TABASCO', '1406', 'PLAZA ATENAS, LOC. 4', 'LAS GALAXIAS', '86035', '99', '3316-6686', '', '', '18.00208', '-92.94855', '-9294855', '-1800208');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('CIUDAD VICTORIA', 'TAMAULIPAS', 'VICTORIA', 'CIUDAD VICTORIA', '28', '041', '0001', '280410001', 'AV LAZARO CARDENAS', '117', 'PLANTA BAJA', 'FRACCIONAMIENTO MEXICO', '87049', '834', '135-0450', '', '', '23.76778', '-99.16101', '-9916101', '-2376778');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('MATAMOROS', 'TAMAULIPAS', 'MATAMOROS', 'HEROICA MATAMOROS', '28', '022', '0001', '280220001', 'AV 6A Y MAGALLANES', '1200', 'ENTRE CALLE J. S. ELCANO Y 4A', 'EUZKADI', '87370', '868', '812-3221', '813-8413', '', '25.86265', '-97.5033', '-9750330', '-2586265');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('NUEVO LAREDO', 'TAMAULIPAS', 'NUEVO LAREDO', 'NUEVO LAREDO', '28', '027', '0001', '280270001', 'BLVD PEDRO PEREZ IBARRA', '4643', 'ENTRE AV. MONTERREY Y AV REFORMA', 'EJIDO DE LA CONCORDIA', '88292', '867', '718-6611', '718-7460', '', '27.44931', '-99.51721', '-9951721', '-2744931');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('REYNOSA', 'TAMAULIPAS', 'REYNOSA', 'REYNOSA', '28', '032', '0001', '280320001', 'GUADALAJARA Y OCCIDENTAL', '265', 'ENTRE RIO MANTE Y HERON RAMIREZ', 'RODRIGUEZ', '88560', '899', '923-2688', '923-26-91', '', '26.07555', '-98.29075', '-9829075', '-2607555');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('TAMPICO', 'TAMAULIPAS', 'TAMPICO', 'TAMPICO', '28', '038', '0001', '280380001', 'CARRETERA TAMPICO-MANTE', '2207', '', 'DEL BOSQUE', '89318', '833', '226-3400', '226-3403', '', '22.31822', '-97.87789', '-9787789', '-2231822');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('TLAXCALA', 'TLAXCALA', 'TLAXCALA', 'TLAXCALA DE XICOHTENCATL', '29', '033', '0001', '290330001', 'BLVD REVOLUCION', '56', '', 'ATEMPAN', '90100', '246', '462-7701', '', '', '19.32461', '-98.21491', '-9821491', '-1932461');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('COATZACOALCOS', 'VERACRUZ', 'COATZACOALCOS', 'COATZACOALCOS', '30', '039', '0001', '300390001', 'NICOLAS BRAVO', '700', 'ESQ. SEBASTIAN LERDO', 'CENTRO', '96400', '921', '212-0688', '213-0971', '', '18.14819', '-94.42391', '-9442391', '-1814819');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('CORDOBA', 'VERACRUZ', 'CORDOBA', 'CORDOBA', '30', '044', '0001', '300440001', 'CALLE 10', '901', 'ESQ. AV. 9', 'SAN JOSE', '94500', '271', '714-7837', '', '', '18.89493', '-96.94093', '-9694093', '-1889493');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('XALAPA', 'VERACRUZ', 'XALAPA', 'XALAPA-ENRIQUEZ', '30', '087', '0001', '300870001', 'AV 20 DE NOVIEMBRE ORIENTE', '659', '', 'ESTHER BADILLO', '91060', '228', '812-5020', '812-5063', '', '19.53176', '-96.91416', '-9691416', '-1953176');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('ORIZABA', 'VERACRUZ', 'ORIZABA', 'ORIZABA', '30', '118', '0001', '301180001', 'PONIENTE 5', '812', '', 'CENTRO', '94300', '272', '725-5145', '', '', '18.84409', '-97.11208', '-9711208', '-1884409');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('POZA RICA', 'VERACRUZ', 'POZA RICA DE HIDALGO', 'POZA RICA DE HIDALGO', '30', '131', '0001', '301310001', 'CONSTITUCION', '203', '', 'TAJIN', '93330', '782', '822-9616', '', '', '20.52865', '-97.46341', '-9746341', '-2052865');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('VERACRUZ', 'VERACRUZ', 'VERACRUZ', 'VERACRUZ', '30', '193', '0001', '301930001', 'AV CUAUHTEMOC', '1200', 'ENTRE J.SILVA Y J.M. GARCIA', 'FORMANDO HOGAR', '91837', '229', '939-6139', '', '', '19.20308', '-96.15759', '-9615759', '-1920308');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('MERIDA', 'YUCATAN', 'MERIDA', 'MERIDA', '31', '050', '0001', '310500001', 'CALLE 7 ', '258', 'ENTRE 42 Y 44 (AV ALFREDO BARRERA V)', 'GARCIA GINERES', '97070', '999', '942-3570', '', '', '20.9952', '-89.64285', '-8964285', '-2099520');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('FRESNILLO', 'ZACATECAS', 'FRESNILLO', 'FRESNILLO', '32', '010', '0001', '320100001', 'AV FRANCISCO GARCIA SALINAS', '408', '', 'CENTRO', '99010', '493', '932-1318', '', '', '23.17321', '-102.8695', '-10286950', '-2317321');
insert into [cesis] 
			([cesi], [estado], [municipio], [localidad], [cv_edo], [cv_mun], [cv_loc], [clave], [calle], [numero], [detalle], [colonia], [cp], [lada], [telefono_1], [telefono_2], [telefono_3], [lat], [lon], [xCoor], [yCoor])
			values ('ZACATECAS', 'ZACATECAS', 'ZACATECAS', 'ZACATECAS', '32', '056', '0001', '320560001', 'CALZ GARCIA SALINAS', '299', '', 'LOMAS DEL CONVENTO', '98060', '492', '924-1275', '', '', '22.76746', '-102.59326', '-10259326', '-2276746');