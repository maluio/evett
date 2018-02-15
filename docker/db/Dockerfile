FROM mysql:5.7

COPY docker-healthcheck.sh /usr/local/bin/docker-healthcheck
RUN chmod +x /usr/local/bin/docker-healthcheck

COPY db-dump.sh /usr/local/bin/db-dump

HEALTHCHECK CMD ["docker-healthcheck"]
