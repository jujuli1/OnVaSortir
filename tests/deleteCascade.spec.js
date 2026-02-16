import { test, expect } from '@playwright/test';
import { execSync } from 'child_process';

test.describe('Utilisateur cascade', () => {
    //  charger les fixtures
    test.beforeAll(() => {
        execSync(
            'docker compose exec php php bin/console doctrine:fixtures:load --env=test --no-interaction',
            { stdio: 'inherit', shell: true }
        );
    });

    test('création et suppression d\'un utilisateur avec cascade', async ({ page }) => {


        // verif connexion utilisateur
        await page.goto('http://localhost:8080/login');
        await page.fill('#inputEmail', 'test@playwright.fr');
        await page.fill('#inputPassword', 'Jujujujuvq2pq&');
        await page.getByRole('button', { name: /connexion|se connecter/i }).click();
        await page.waitForLoadState('networkidle');

        // redirection vers profil
        await expect(page).toHaveURL('http://localhost:8080/profil');
        await expect(page.getByText('Bonjour')).toBeVisible();

        // suprression utilisateur
        await page.request.post('http://localhost:8080/test/delete-user', {
            data: { email: 'test@playwright.fr' }
        });


        // Vérifier via HTTP ou UI que les inscriptions ont bien été supprimées
        const response = await page.request.get('http://localhost:8080/test/count-inscriptions');
        const data = await response.json(); // { count: 0 }
        expect(data.count).toBe(0);

    });
});
