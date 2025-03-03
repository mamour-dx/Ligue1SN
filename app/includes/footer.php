    </main>
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>Réalisé par</h3>
                <p>Mamour, Ndeye Awa, Abdourahmane, Ousmane et Maïmouna</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> Ligue 1 Sénégalaise. Tous droits réservés.</p>
        </div>
    </footer>
    <script src="/js/main.js"></script>
    <style>
        .footer {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
            padding: 1.5rem 0 0;
            margin-top: 2rem;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            text-align: center;
        }

        .footer-section h3 {
            color: white;
            margin-bottom: 0.5rem;
            font-size: 1.2rem;
        }

        .footer-section p {
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.4;
        }

        .footer-bottom {
            margin-top: 1.5rem;
            padding: 1rem 0;
            text-align: center;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .footer-bottom p {
            margin: 0;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
        }
    </style>
</body>
</html> 