Stock Exchange App
===

Sample app that grabs real-time stock market data, allowing users to add funds, buy and sell shares.

Running the project locally
---

To run the project locally, first clone the repository:
```sh
git clone git@github.com:luads/ls-stock-app.git
```

You will need to configure an [Alpha Vantage API](https://www.alphavantage.co/support/#api-key) key on your environment file. First, copy the file, then add your key to the `ALPHA_VANTAGE_API_KEY` param:
```sh
copy backend/.env backend/.env.local
vim backend/.env.local
```

Then to build and run the docker images, go inside the root folder and run:
```sh
make run
```

The UI will be available at [http://localhost:5000](http://localhost:5000), or simply run:
```sh
make open
```

Development mode
---
To run the UI in development mode, you need to run the following commands:
```sh
cd frontend
npm install
npm run start
```

API Endpoints
---
You can check the API docs [here](https://documenter.getpostman.com/view/133880/SzYZ2JxL?version=latest). 

Overall architecture
---

The project has isolated API and UI components. It also has a SQLite database layer and a Redis cache so the external API is not flooded with requests.

![C4 Level 2 - Stock App](docs/resources/components.png?raw=true)
