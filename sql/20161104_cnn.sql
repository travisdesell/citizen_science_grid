CREATE TABLE IF NOT EXISTS cnn_preprocess (
    id INT NOT NULL AUTO_INCREMENT,
    method VARCHAR(255) NOT NULL,

    PRIMARY KEY(id)
);

CREATE TABLE IF NOT EXISTS cnn (
    id INT NOT NULL AUTO_INCREMENT,
    location VARCHAR(255) NOT NULL,
    cnn_preprocess_id INT NOT NULL,

    PRIMARY KEY(id),
    INDEX(cnn_preprocess_id),

    FOREIGN KEY(cnn_preprocess_id)
        REFERENCES cnn_preprocess(id)
);

CREATE TABLE IF NOT EXISTS cnn_config (
    id INT NOT NULL AUTO_INCREMENT,
    cnn_id INT NOT NULL,
    params VARCHAR(64),

    CONSTRAINT uc_cnn_params
        UNIQUE(cnn_id, params),

    PRIMARY KEY(id),
    INDEX(cnn_id),

    FOREIGN KEY(cnn_id)
        REFERENCES cnn(id)
);

CREATE TABLE IF NOT EXISTS event_type (
    id INT NOT NULL AUTO_INCREMENT,
    event VARCHAR(64) NOT NULL,

    PRIMARY KEY(id)
);

CREATE TABLE IF NOT EXISTS cnn_observations (
    id INT NOT NULL AUTO_INCREMENT,
    cnn_config_id INT NOT NULL,
    video_id INT NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    event_type_id INT NOT NULL,

    PRIMARY KEY(id),

    FOREIGN KEY(cnn_config_id)
        REFERENCES cnn_config(id),

    FOREIGN KEY(event_type_id)
        REFERENCES event_type(id)
);
