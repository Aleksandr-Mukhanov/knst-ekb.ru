create table if not exists catalog_app_settings (
    ID int(11) not null auto_increment,
    CATALOG_APP_CATALOG_ID int(11) not null,
    CATALOG_APP_USER varchar(255) not null,
    CATALOG_APP_PASSWORD varchar(255) not null,
    CATALOG_APP_TOKEN varchar(255) default null,
    CATALOG_APP_TOKEN_EXPIRE timestamp NULL DEFAULT NULL,
    CATALOG_IBLOCK_ID int(11) not null,
    CREATE_WORKER int(11) not null default 0,
    UPDATE_WORKER int(11) not null default 0,
    UPDATE_PROPERTY_WORKER int(11) not null default 0,
    AUTO_UPDATE_GOODS int(11) not null default 0,
    AUTO_UPDATE_RULES text,
    primary key (ID)
);

create table if not exists catalog_app_workers (
    ID int(11) not null auto_increment,
    WORKER_ID varchar(255) not null,
    BUSY int(1) not null default 0,
    TIME_START varchar(255),
    TIME_END varchar(255),
    NEED_START int(1) not null default 0,
    primary key (ID)
);

insert into catalog_app_workers (`WORKER_ID`) VALUES ("create");
insert into catalog_app_workers (`WORKER_ID`) VALUES ("update_price");
insert into catalog_app_workers (`WORKER_ID`) VALUES ("update");

create table if not exists catalog_app_rules (
    ID int(11) not null auto_increment,
    SITE_PROFILE_ID int(11) not null default 0,
    RULES varchar(255),
    PRICE_ID int(11) not null default 0,
    CATALOG_APP_ID int(11) not null default 0,
    primary key (ID)
);

create table if not exists catalog_app_tasks (
    ID int(11) not null auto_increment,
    TYPE int(11) not null default 0,
    TASK_ID int(11) not null default 0,
    STATUS int(11) not null default 0,
    ADD_DATE varchar(255),
    UPDATE_DATE varchar(255),
    NEED_START int(1) not null default 1,
    primary key (ID)
);

create table if not exists catalog_app_data (
    ID int(11) not null auto_increment,
    GOODS_XML_ID varchar (255),
    GOODS_SITE_ID int(11) not null default 0,
    CATALOG_APP_ID int(11) not null default 0,
    DELIVERY_TIME int(11) not null default 0,
    DELIVERY_COUNTRY_TIME int(11) not null default 0,
    DELIVERY_PRICE int(11) not null default 0,
    DELIVERY_COUNTRY_PRICE int(11) not null default 0,
    INSTALLMENT_PRICE decimal (13, 2) not null default 0,
    MAX_INSTALLMENT_COST decimal (13, 2) not null default 0,
    MIN_RETAIL_PRICE fdecimal (13, 2) not null default 0,
    PRICE decimal (13, 2) not null default 0,
    ORIGINAL_PRICE decimal (13, 2) not null default 0,
    PROFIT decimal (13, 2) not null default 0,
    CURRENCY varchar(255),

    IN_STOCK_AMOUNT int(11) not null default 0,
    SITE_CATEGORY_ID int(11) not null default 0,
    CATALOG_APP_CATEGORY varchar(255),
    VENDOR varchar(255),
    MODEL varchar(255),
    ARTICLE varchar(255),
    COMMENT text,
    PRODUCER text,
    IMPORTER text,
    SERVICE_CENTERS text,
    WARRANTY int(11) not null default 0,
    PRODUCT_LIFE_TIME int(11) not null default 0,
    COLOR varchar (255),
    EAN varchar (255),
    SITE_PROFILE_ID int(11) not null default 0,
    SUPPLIER_ID varchar (255),
    primary key (ID)
);

create table if not exists catalog_app_section_connections (
    ID int(11) not null auto_increment,
    CATALOG_APP_SECTION_NAME varchar(255),
    CATALOG_APP_SECTION_ID int(11) not null default 0,
    SITE_SECTION_NAME varchar(255),
    SITE_SECTION_ID int(1) not null default 0,
    primary key (ID)
);
