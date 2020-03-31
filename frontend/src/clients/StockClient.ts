import axios, {AxiosInstance} from 'axios';

export default class StockClient {
  private httpClient: AxiosInstance;
  private readonly host: string = 'https://ls-stock-api.herokuapp.com';

  constructor() {
    if (process.env.REACT_APP_STOCK_API_HOST) {
      this.host = process.env.REACT_APP_STOCK_API_HOST;
    }

    this.httpClient = axios.create({
      baseURL: this.host,
      timeout: 10000,
      headers: {
        'Content-type': 'application/json',
      }
    });
  }

  async balance(user: string): Promise<number> {
    const response = await this.httpClient.get('/v1/balance', { headers: { 'X-User': user }});

    return parseFloat(response.data.balance);
  }
}
