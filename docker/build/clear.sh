#!/usr/bin/env bash

# Exit the script if any statement returns a non-true return value
set -e

apt autoremove -y 2&>1 >/dev/null || true
apt clean -y 2&>1 >/dev/null || true
apt autoclean -y 2&>1 >/dev/null || true

rm -rf \
	/app/storage/framework/views/* \
	/app/storage/app/public/* \
	/app/storage/logs/* \
	/app/storage/framework/cache/* \
	/var/lib/apt/lists/* \
	/tmp/* \
	/var/tmp/* \
	/var/cache \
	/opt/docker/etc/nginx/sites-enabled/default \
	/opt/docker/etc/nginx/conf.d/nginx_status_location \
	/entypoint.sh

mkdir -p /var/cache/nginx/

find /var/log -type f | while read f; do
	echo -ne '' > ${f} 2&>1 > /dev/null || true;
done

mkdir -p /var/cache/apt

if [ -f /app/.env ]; then
	rm -f /app/.env
fi

exit 0
