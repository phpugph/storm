CREATE TABLE IF NOT EXISTS address
(
  address_id serial NOT NULL,
  address_label character varying(255) DEFAULT NULL,
  address_street character varying(255) NOT NULL,
  address_neighborhod character varying(255) DEFAULT NULL,
  address_city character varying(255) NOT NULL,
  address_state character varying(255) DEFAULT NULL,
  address_region character varying(255) DEFAULT NULL,
  address_country character varying(255) NOT NULL,
  address_postal character varying(255) NOT NULL,
  address_landmarks character varying(255) DEFAULT NULL,

  CONSTRAINT address_pk PRIMARY KEY (address_id),
  CONSTRAINT address_address_id_check CHECK (address_id >= 0)
);
