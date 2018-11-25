drop table facttable;
drop table currency_dim;
drop table oilprice_dim;
drop table country_dim;
drop table product_dim;
drop table fact_aggregated;
drop table facttable_USDP_Barrels;

create table product_dim (
  pid int,
  pname varchar(50) not null,
  primary key(pid)
);

create table country_dim (
  cid char(2),
  cname varchar(50) not null,
  primary key(cid)
);

create table oilprice_dim(
  month int not null check (month > 0 and month < 13),
  year int not null check (year > 1970 and year < 2100),
  barrelprice numeric(8,3) not null check (barrelprice > 1 and barrelprice < 500),
  primary key(year, month)
);
create table currency_dim (
  month int not null check (month > 0 and month < 13),
  year int not null check (year > 1970 and year < 2100),
  usdprice numeric(8,3) not null check (usdprice > 1 and usdprice < 20),
  primary key(year, month)
);

create table facttable (
  pid int,
  cid char(2),
  month int not null check (month > 0 and month < 13),
  year int not null check (year > 1970 and year < 2100),
  amountMNOK numeric(10,2) check (amountMNOK >= 0 and amountMNOK < 1000000),
  foreign key (pid) references product_dim(pid),
  foreign key (cid) references country_dim(cid),
  primary key(pid,cid,month,year)
);


create table facttable_USDP_Barrels (
  pid int,
  cid char(2),
  month int not null check (month > 0 and month < 13),
  year int not null check (year > 1970 and year < 2100),
  amountMNOK numeric(10,2),
  amountMUSD numeric(10,2),
  kbarrels numeric(10,2),
  foreign key (pid) references product_dim(pid),
  foreign key (cid) references country_dim(cid),
  primary key(pid,cid,month,year)
);


create table fact_aggregated (
  pid int,
  cid char(2),
  year int not null check (year > 1970 and year < 2100),
  amountMNOK numeric(12,2),
  amountMUSD numeric(12,2),
  kbarrels numeric(12,2),
  foreign key (pid) references product_dim(pid),
  foreign key (cid) references country_dim(cid),
  primary key(pid,cid,year)
);
