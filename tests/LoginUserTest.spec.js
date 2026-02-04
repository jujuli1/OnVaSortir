import { test, expect } from '@playwright/test';
import { execSync } from 'child_process';

// Recharger les fixture avant tous les tests
test.beforeAll(() => {
    execSync(
        'docker compose exec php php bin/console doctrine:fixtures:load --env=test --no-interaction',
        { stdio: 'inherit' }
    );
});

test.describe('Login utilisateur', () => {

    test('connexion réussie', async ({ page }) => {
        // ---> page login
        await page.goto('http://localhost:8080/login');

        // rempli le formulaire
        await page.fill('#inputEmail', 'test@playwright.fr');
        await page.fill('#inputPassword', 'Jujujujuvq2pq&');


        await page.getByRole('button', { name: /connexion|se connecter/i }).click();

        // temp d'attente
        await page.waitForLoadState('networkidle');

        //redirection vers /profil
        await expect(page).toHaveURL('http://localhost:8080/profil');

        // Chercher "Bonjour" sur la page de profil
        await expect(page.getByText('Bonjour')).toBeVisible();
    });

    test('connexion échouée', async ({ page }) => {

        await page.goto('http://localhost:8080/login');


        await page.fill('#inputEmail', 'test@playwright.fr');
        await page.fill('#inputPassword', 'mauvaismotdepasse');

        await page.getByRole('button', { name: /connexion|se connecter/i }).click();


        await expect(page).toHaveURL('http://localhost:8080/login');

        // Vérifier message d'erreur
        await expect(page.locator('.alert')).toBeVisible();
    });
});
