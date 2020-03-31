import React, {useEffect, useState} from 'react';
import StockClient from '../../clients/StockClient';
import NumberFormat from "react-number-format";

interface Props {
  user: string;
  stockClient: StockClient;
  setIsLoading(isLoading: boolean): void;
}

export default function Balance({user, stockClient, setIsLoading}: Props) {
  const [balance, setBalance] = useState<number>(0);

  useEffect(() => {
    const fetchBalance = async () => {
      setBalance(await stockClient.balance(user));
      setIsLoading(false);
    };

    fetchBalance();
  }, []);

  return (
    <>
      Your balance is <strong>
      <NumberFormat value={balance} displayType={'text'} thousandSeparator={true} decimalScale={2}
                    fixedDecimalScale={true} prefix={'$'} />
    </strong>.
    </>
  );
}
