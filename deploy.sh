#!/bin/bash
set -e

echo "========================================="
echo "  Deploy GeoContacts - $(date)"
echo "========================================="

# 1. Rebuild dos containers
echo "[1/5] Rebuild dos containers Docker..."
docker compose -f docker-compose.prod.yml build --no-cache

# 2. Subir containers (recria se houver mudanças)
echo "[2/5] Subindo containers..."
docker compose -f docker-compose.prod.yml up -d --force-recreate

# 3. Rodar migrations
echo "[3/5] Rodando migrations..."
docker compose -f docker-compose.prod.yml exec -T app php artisan migrate --force

# 4. Cache de configuração e rotas
echo "[4/5] Otimizando para produção..."
docker compose -f docker-compose.prod.yml exec -T app php artisan config:cache
docker compose -f docker-compose.prod.yml exec -T app php artisan route:cache
docker compose -f docker-compose.prod.yml exec -T app php artisan view:cache

# 5. Limpar imagens antigas
echo "[5/5] Limpando imagens não utilizadas..."
docker image prune -f

echo "========================================="
echo "  Deploy concluído com sucesso!"
echo "========================================="