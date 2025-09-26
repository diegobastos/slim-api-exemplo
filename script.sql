create schema exemplo_api;
use exemplo_api;

create table if not exists users (
	id bigint not null auto_increment,
    created_at timestamp default current_timestamp(),
    updated_at timestamp default current_timestamp() on update current_timestamp(),
    created_by varchar(100),
    updated_by varchar(100),
    name varchar(100) not null,
    username varchar(100) not null,
    email varchar(100) not null,
    password_hash varchar(255) not null,
    primary key (id)
);

create table if not exists address (
    id bigint not null auto_increment,
    created_at timestamp default current_timestamp(),
    updated_at timestamp default current_timestamp() on update current_timestamp(),
    created_by varchar(100),
    updated_by varchar(100),

    user_id bigint not null,
    street varchar(150) not null,
    num varchar(20),
    complement varchar(100),
    neighborhood varchar(100) not null,
    city varchar(100) not null,
    state varchar(100) not null,
    country varchar(100) not null,
    zip_code varchar(20) not null,

    primary key (id),
    foreign key (user_id) references users(id)
        on delete cascade
        on update cascade
);

create table if not exists tasks (
    id bigint not null auto_increment,
    created_at timestamp default current_timestamp(),
    updated_at timestamp default current_timestamp() on update current_timestamp(),
    created_by varchar(100),
    updated_by varchar(100),

    user_id bigint not null,
    name varchar(255),
    description TEXT,
    is_completed tinyint(1) default 0,
    
    primary key (id),
    foreign key (user_id) references users(id)
        on delete cascade
        on update cascade    
);