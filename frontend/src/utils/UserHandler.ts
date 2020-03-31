export default class UserHandler {
  private LOCAL_STORAGE_USER_KEY: string = 'ls_stock.username';

  authenticate (username: string): void {
    localStorage.setItem(this.LOCAL_STORAGE_USER_KEY, username);
  }

  logout (): void {
    localStorage.removeItem(this.LOCAL_STORAGE_USER_KEY);
  }

  getLoggedInUser (): string|null {
    return localStorage.getItem(this.LOCAL_STORAGE_USER_KEY);
  }
}
