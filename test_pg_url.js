const { Client } = require('pg');
const client = new Client({
    connectionString: 'postgresql://postgres.bpgcpdzdwdxitmlwnshu:Shikur%403828%40@aws-0-eu-west-1.pooler.supabase.com:6543/postgres'
});
client.connect()
  .then(() => {
    console.log('CONNECTED PERFECTLY!');
    return client.query('SELECT current_database(), current_user');
  })
  .then(res => {
    console.log(res.rows);
    process.exit(0);
  })
  .catch(err => {
    console.error('ERROR:', err);
    process.exit(1);
  });
