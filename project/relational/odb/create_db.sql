drop table sales; 
drop table country;
drop table product; 

create table product (
  pid int,
  pname varchar(50),
  primary key(pid)
);

create table country (
  cid char(2),
  cname varchar(50),
  primary key(cid)
);

create table sales (
  pid int,
  cid char(2),
  foreign key (pid) references product(pid),
  foreign key (cid) references country(cid),
  month int not null check (month > 0 and month < 13),
  year int not null check (year > 1970 and year < 2100),
  amountMNOK numeric(10,2) check (amountMNOK >= 0 and amountMNOK < 100000),
  primary key(pid,cid,month,year)
);

select p.pname, c.cname, s.year, s.month, sum(s.amountMNOK) as amount
from sales s, product p, country c
where s.pid = p.pid
and s.cid = c.cid
group by s.pid, s.cid, s.month, s.year
order by c.cname, s.year, s.month

select p.pname, c.cname, s.year, s.month, s.amountMNOK as amount
from sales s, product p, country c
where s.pid = p.pid
and s.cid = c.cid
order by c.cname, s.year, s.month

select p.pname, sum(s.amountMNOK) as amount
from sales s, product p
where s.pid = p.pid
group by s.pid
