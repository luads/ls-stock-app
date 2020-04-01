import axios, {AxiosInstance} from 'axios';
import Share from '../interfaces/Share';

export default class StockClient {
  private httpClient: AxiosInstance;
  private readonly host: string = 'https://ls-stock-api.herokuapp.com';

  constructor() {
    if (process.env.REACT_APP_STOCK_API_HOST) {
      this.host = process.env.REACT_APP_STOCK_API_HOST;
    }

    this.httpClient = axios.create({
      baseURL: this.host,
      timeout: 30000, // Heroku can be slow when warming up :)
      headers: {
        'Content-type': 'application/json',
      }
    });
  }

  async getBalance(user: string): Promise<number> {
    const response = await this.httpClient.get('/v1/balance', { headers: { 'X-User': user } });

    return parseFloat(response.data.balance);
  }

  async sendTransaction(user: string, balance: number): Promise<number> {
    const response = await this.httpClient.post(
      '/v1/balance/transaction',
      { balance },
      { headers: { 'X-User': user } }
    );

    return parseFloat(response.data.balance);
  }

  async getHoldings(user: string): Promise<Share[]> {
    const response = await this.httpClient.get('/v1/shares', { headers: { 'X-User': user } });

    return response.data as Share[];
  }
}
