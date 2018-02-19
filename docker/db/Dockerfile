FROM mysql:5.7.9

#Minmize memory usage of mysql container
COPY minimal.cnf /etc/mysql/conf.d/

COPY docker-healthcheck.sh /usr/local/bin/docker-healthcheck
RUN chmod +x /usr/local/bin/docker-healthcheck

COPY db-dump.sh /usr/local/bin/db-dump

HEALTHCHECK CMD ["docker-healthcheck"]
