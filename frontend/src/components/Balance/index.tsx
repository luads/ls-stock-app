import React from 'react';
import NumberFormat from "react-number-format";

interface Props {
  balance: number;
}

export default function Balance({balance}: Props) {
  return (
    <>
      Your balance is <strong>
      <NumberFormat
        value={balance}
        displayType="text"
        thousandSeparator={true}
        decimalScale={2}
        fixedDecimalScale={true}
        prefix="$" />
      </strong>.
    </>
  );
}
