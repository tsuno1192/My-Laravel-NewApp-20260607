# データベース接続などのartisanコマンド (例: art migrate)
echo "alias artisan='docker compose exec laravel.test php artisan'" >> ~/.bashrc

# フロントエンドのコンパイル (例: dev)
echo "alias dev='docker compose exec laravel.test npm run dev'" >> ~/.bashrc

# コンテナのログを見る (例: logs)
echo "alias logs='docker compose logs -f laravel.test'" >> ~/.bashrc

# キャッシュのクリア (例: clear)
echo "alias clear='docker compose exec laravel.test php artisan optimize:clear'" >> ~/.bashrc

# 設定を反映させる
source ~/.bashrc
