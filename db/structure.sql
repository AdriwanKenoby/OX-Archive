create table t_user (
    usr_id integer not null primary key autoincrement,
    usr_name varchar(50) not null,
    usr_password varchar(88) not null,
    usr_salt varchar(23) not null,
    usr_role varchar(50) not null
);
