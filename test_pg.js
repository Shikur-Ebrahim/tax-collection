const { Client } = require('pg');
const regions = ['us-east-1', 'us-west-1', 'eu-west-1', 'eu-central-1', 'eu-west-2', 'ap-south-1', 'ap-southeast-1', 'ap-southeast-2', 'sa-east-1', 'ca-central-1', 'ap-northeast-1', 'ap-northeast-2'];
const pass = 'Shikur@3828@';
const user = 'postgres.bpgcpdzdwdxitmlwnshu';

async function testRegion(region) {
    const host = 'aws-0-' + region + '.pooler.supabase.com';
    const client = new Client({
        host: host,
        port: 6543,
        user: user,
        password: pass,
        database: 'postgres',
        ssl: { rejectUnauthorized: false },
        connectionTimeoutMillis: 5000
    });
    try {
        await client.connect();
        console.log('SUCCESS: ' + host);
        await client.end();
        process.exit(0);
    } catch (e) {
        if (!e.message.includes('timeout') && !e.message.includes('password authentication failed')) {
             console.log('FAIL ' + host + ': ' + e.message);
        }
    }
}

async function run() {
    console.log('Testing regions...');
    const promises = regions.map(r => testRegion(r));
    await Promise.allSettled(promises);
    console.log('Done testing.');
}
run();
