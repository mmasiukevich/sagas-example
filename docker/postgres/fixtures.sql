CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
CREATE TABLE IF NOT EXISTS sagas_store
(
    id               UUID,
    identifier_class VARCHAR   NOT NULL,
    saga_class       VARCHAR   NOT NULL,
    payload          BYTEA     NOT NULL,
    state_id         VARCHAR   NOT NULL,
    created_at       TIMESTAMP NOT NULL,
    expiration_date  TIMESTAMP NOT NULL,
    closed_at        TIMESTAMP,
    CONSTRAINT saga_identifier PRIMARY KEY (id, identifier_class)
);

CREATE INDEX IF NOT EXISTS sagas_state ON sagas_store (state_id);
CREATE INDEX IF NOT EXISTS saga_closed_index ON sagas_store (state_id, closed_at);

CREATE TABLE IF NOT EXISTS customers
(
    id           UUID,
    email        VARCHAR   NOT NULL,
    login        VARCHAR   NOT NULL,
    password     VARCHAR   NOT NULL,
    created_at   TIMESTAMP NOT NULL,
    confirmed_at TIMESTAMP,
    CONSTRAINT customer_identifier PRIMARY KEY (id)
);