import axios, {AxiosInstance, AxiosResponse} from 'axios';
import Share from '../interfaces/Share';
import RateLimitedApiError from '../errors/RateLimitedApiError';
import ShareDetail from '../interfaces/ShareDetail';
import InsuficcientFundsError from '../errors/InsuficcientFundsError';

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
      validateStatus: () => true,
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

    if (response.status === 400) {
      throw new InsuficcientFundsError(response.data.message);
    }

    return parseFloat(response.data.balance);
  }

  async getHoldings(user: string): Promise<Share[]> {
    const response = await this.httpClient.get('/v1/shares', { headers: { 'X-User': user } });

    return response.data as Share[];
  }

  async getShare(user: string, name: string): Promise<ShareDetail | null> {
    const response = await this.httpClient.get(
      `/v1/shares/${name.toLocaleUpperCase()}`,
      { headers: { 'X-User': user } });

    this.checkForRateLimitedResponse(response);

    if (response.status === 404) {
      return null;
    }

    return response.data as ShareDetail;
  }

  async purchase(user: string, share: string, quantity: number): Promise<number> {
    const response = await this.httpClient.post(
      `/v1/shares/${share}/purchase`,
      { quantity },
      { headers: { 'X-User': user } }
    );

    this.checkForRateLimitedResponse(response);

    if (response.status === 400) {
      throw new InsuficcientFundsError(response.data.message);
    }

    const { transaction_value } = response.data;

    return parseFloat(transaction_value);
  }

  async sell(user: string, share: string, quantity: number): Promise<number> {
    const response = await this.httpClient.post(
      `/v1/shares/${share}/sell`,
      { quantity },
      { headers: { 'X-User': user } }
    );

    this.checkForRateLimitedResponse(response);

    if (response.status === 400) {
      throw new InsuficcientFundsError(response.data.message);
    }

    const { transaction_value } = response.data;

    return parseFloat(transaction_value);
  }

  private checkForRateLimitedResponse(response: AxiosResponse): void {
    if (response.status === 429) {
      throw new RateLimitedApiError(`The API reached it's rate limit, try again in a few seconds`);
    }
  }
}
